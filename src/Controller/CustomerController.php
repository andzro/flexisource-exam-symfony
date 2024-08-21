<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    #[Route('/customers', name: 'get_customers', methods: ['GET'])]
    public function getCustomerEntities(): JsonResponse
    {
        $customers = $this->customerService->getCustomers();
        $customerArray = [];

        foreach ($customers as $customer) {
            $customerArray[] = [
                'fullName' => $customer->getFirstName() . ' ' . $customer->getLastName(),
                'email' => $customer->getEmail(),
                'country' => $customer->getCountry(),
            ];
        }

        return new JsonResponse($customerArray);
    }

    #[Route('/customers/{id}', name: 'get_customer', methods: ['GET'])]
    public function getCustomerEntity(int $id): JsonResponse
    {
        $customer = $this->customerService->findCustomerById($id);

        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'fullName' => $customer->getFirstName() . ' ' . $customer->getLastName(),
            'email' => $customer->getEmail(),
            'username' => $customer->getUsername(),
            'gender' => $customer->getGender(),
            'country' => $customer->getCountry(),
            'city' => $customer->getCity(),
            'phone' => $customer->getPhone(),
        ]);
    }
}
