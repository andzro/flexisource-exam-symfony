<?php

namespace App\Service;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CustomerService
{
    private CustomerRepository $customerRepository;
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;

    public function __construct(CustomerRepository $customerRepository, EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    public function importCustomers(string $url = 'https://randomuser.me/api?results=100&nat=AU'): void
    {
        $response = $this->httpClient->request('GET', $url);

        $data = $response->toArray();

        foreach ($data['results'] as $customerData) {
            $email = $customerData['email'];
            $username = $customerData['login']['username'];
            $gender = $customerData['gender'];
            $country = $customerData['location']['country'];
            $city = $customerData['location']['city'];
            $phone = $customerData['phone'];

            $customer = $this->customerRepository->findOneBy(['email' => $email]) ?? new Customer();

            $customer->setEmail($email);
            $customer->setFirstName($customerData['name']['first']);
            $customer->setLastName($customerData['name']['last']);
            $customer->setUsername($username);
            $customer->setGender($gender);
            $customer->setCountry($country);
            $customer->setCity($city);
            $customer->setPhone($phone);
            $customer->setPassword(md5('default_password')); // Not secure, but as requested

            $this->entityManager->persist($customer);
        }

        $this->entityManager->flush();
    }

    public function getCustomers(): array
    {
        return $this->customerRepository->findAll();
    }

    public function createCustomer(
        string $email,
        string $password,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $username = null,
        ?string $gender = null,
        ?string $country = null,
        ?string $city = null,
        ?string $phone = null
    ): Customer
    {
        $customer = new Customer();
        $customer->setEmail($email);
        $customer->setPassword($password);
        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setUsername($username);
        $customer->setGender($gender);
        $customer->setCountry($country);
        $customer->setCity($city);
        $customer->setPhone($phone);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $customer;
    }

    public function findCustomerById(int $id): ?Customer
    {
        return $this->customerRepository->findOneBy(['id' => $id]);
    }
}
