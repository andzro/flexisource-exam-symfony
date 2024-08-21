<?php

namespace App\Tests\Service;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Service\CustomerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CustomerServiceTest extends TestCase
{
    private $customerRepository;
    private $entityManager;
    private $httpClient;
    private $customerService;

    protected function setUp(): void
    {
        $this->customerRepository = $this->createMock(CustomerRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);

        $this->customerService = new CustomerService(
            $this->customerRepository,
            $this->entityManager,
            $this->httpClient
        );
    }

    public function testImportCustomers()
    {
        $responseData = [
            'results' => [
                [
                    'email' => 'test@example.com',
                    'login' => ['username' => 'testuser'],
                    'gender' => 'male',
                    'location' => [
                        'country' => 'AU',
                        'city' => 'Sydney',
                    ],
                    'phone' => '123456789',
                    'name' => ['first' => 'John', 'last' => 'Doe']
                ]
            ]
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')
                 ->willReturn($responseData);

        $this->httpClient->method('request')
                         ->willReturn($response);

        $customer = $this->createMock(Customer::class);

        $this->customerRepository->method('findOneBy')
                                 ->with(['email' => 'test@example.com'])
                                 ->willReturn(null);

        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($this->isInstanceOf(Customer::class));

        $this->entityManager->expects($this->once())
                            ->method('flush');

        $this->customerService->importCustomers();
    }

    public function testGetCustomers()
    {
        $customers = [
            $this->createMock(Customer::class),
            $this->createMock(Customer::class),
        ];

        $this->customerRepository->method('findAll')
                                 ->willReturn($customers);

        $result = $this->customerService->getCustomers();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testCreateCustomer()
    {
        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($this->isInstanceOf(Customer::class));

        $this->entityManager->expects($this->once())
                            ->method('flush');

        $customer = $this->customerService->createCustomer(
            'test@example.com',
            'password',
            'John',
            'Doe',
            'testuser',
            'male',
            'AU',
            'Sydney',
            '123456789'
        );

        $this->assertInstanceOf(Customer::class, $customer);

        $this->assertEquals('test@example.com', $customer->getEmail());
        $this->assertEquals('John', $customer->getFirstName());
        $this->assertEquals('Doe', $customer->getLastName());
    }

    public function testFindCustomerById()
    {
        $customer = $this->createMock(Customer::class);

        $this->customerRepository->method('findOneBy')
                                 ->with(['id' => 1])
                                 ->willReturn($customer);

        $result = $this->customerService->findCustomerById(1);

        $this->assertSame($customer, $result);
    }

    public function testImportCustomersApiFailure()
    {
        $this->httpClient->method('request')
                         ->will($this->throwException(new \Exception('API request failed')));

        $this->entityManager->expects($this->never())
                            ->method('persist');
        $this->entityManager->expects($this->never())
                            ->method('flush');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API request failed');

        $this->customerService->importCustomers();
    }

    public function testImportCustomersWithDuplicateEmail()
    {
        $responseData = [
            'results' => [
                [
                    'email' => 'duplicate@example.com',
                    'login' => ['username' => 'testuser'],
                    'gender' => 'male',
                    'location' => [
                        'country' => 'AU',
                        'city' => 'Sydney',
                    ],
                    'phone' => '123456789',
                    'name' => ['first' => 'John', 'last' => 'Doe']
                ]
            ]
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')
                 ->willReturn($responseData);

        $this->httpClient->method('request')
                         ->willReturn($response);

        $existingCustomer = $this->createMock(Customer::class);
        $this->customerRepository->method('findOneBy')
                                 ->with(['email' => 'duplicate@example.com'])
                                 ->willReturn($existingCustomer);

        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($existingCustomer);
        $this->entityManager->expects($this->once())
                            ->method('flush');

        $this->customerService->importCustomers();
    }

    public function testFindNonExistentCustomerById()
    {
        $this->customerRepository->method('findOneBy')
                                 ->with(['id' => 9999])
                                 ->willReturn(null);

        $result = $this->customerService->findCustomerById(9999);

        $this->assertNull($result);
    }
}
