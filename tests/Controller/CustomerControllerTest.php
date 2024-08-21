<?php

namespace App\Tests\Controller;

use App\Entity\Customer;
use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CustomerControllerTest extends WebTestCase
{
    private $client;
    private $customerService;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->customerService = $this->createMock(CustomerService::class);
        $this->client->getContainer()->set(CustomerService::class, $this->customerService);
    }

    public function testGetCustomerEntitiesWithResult()
    {
        $customer1 = new Customer();
        $customer1->setFirstName('John')->setLastName('Doe')->setEmail('john@example.com')->setCountry('AU');

        $customer2 = new Customer();
        $customer2->setFirstName('Jane')->setLastName('Smith')->setEmail('jane@example.com')->setCountry('AU');

        $this->customerService->method('getCustomers')->willReturn([$customer1, $customer2]);

        $this->client->request('GET', '/customers');

        $this->assertResponseIsSuccessful();
        $responseContent = $this->client->getResponse()->getContent();
        $responseArray = json_decode($responseContent, true);

        $this->assertCount(2, $responseArray);
        $this->assertEquals('John Doe', $responseArray[0]['fullName']);
        $this->assertEquals('john@example.com', $responseArray[0]['email']);
        $this->assertEquals('AU', $responseArray[0]['country']);
        $this->assertEquals('Jane Smith', $responseArray[1]['fullName']);
    }

    public function testGetCustomerEntitiesEmpty()
    {
        $this->customerService->method('getCustomers')->willReturn([]);

        $this->client->request('GET', '/customers');

        $this->assertResponseIsSuccessful();
        $responseContent = $this->client->getResponse()->getContent();
        $responseArray = json_decode($responseContent, true);

        $this->assertEmpty($responseArray);
    }

    public function testGetCustomerEntityWithResult()
    {
        $customer = new Customer();
        $customer->setFirstName('John')
                 ->setLastName('Doe')
                 ->setEmail('john@example.com')
                 ->setUsername('johndoe')
                 ->setGender('male')
                 ->setCountry('AU')
                 ->setCity('Sydney')
                 ->setPhone('123456789');

        $this->customerService->method('findCustomerById')->willReturn($customer);

        $this->client->request('GET', '/customers/1');

        $this->assertResponseIsSuccessful();
        $responseContent = $this->client->getResponse()->getContent();
        $responseArray = json_decode($responseContent, true);

        $this->assertEquals('John Doe', $responseArray['fullName']);
        $this->assertEquals('john@example.com', $responseArray['email']);
        $this->assertEquals('johndoe', $responseArray['username']);
        $this->assertEquals('male', $responseArray['gender']);
        $this->assertEquals('AU', $responseArray['country']);
        $this->assertEquals('Sydney', $responseArray['city']);
        $this->assertEquals('123456789', $responseArray['phone']);
    }

    public function testGetCustomerEntityNotFound()
    {
        $this->customerService->method('findCustomerById')->willReturn(null);

        $this->client->request('GET', '/customers/9999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $responseContent = $this->client->getResponse()->getContent();
        $responseArray = json_decode($responseContent, true);

        $this->assertEquals('Customer not found', $responseArray['error']);
    }
}
