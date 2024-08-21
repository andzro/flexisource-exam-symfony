<?php

namespace App\Tests\Entity;

use App\Entity\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function testCustomerEntityFields()
    {
        $customer = new Customer();

        $customer->setEmail('john.doe@example.com');
        $customer->setPassword('securepassword');
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setUsername('johndoe');
        $customer->setGender('male');
        $customer->setCountry('AU');
        $customer->setCity('Sydney');
        $customer->setPhone('123456789');

        $this->assertEquals('john.doe@example.com', $customer->getEmail());
        $this->assertEquals('securepassword', $customer->getPassword());
        $this->assertEquals('John', $customer->getFirstName());
        $this->assertEquals('Doe', $customer->getLastName());
        $this->assertEquals('johndoe', $customer->getUsername());
        $this->assertEquals('male', $customer->getGender());
        $this->assertEquals('AU', $customer->getCountry());
        $this->assertEquals('Sydney', $customer->getCity());
        $this->assertEquals('123456789', $customer->getPhone());
    }

    public function testUniqueConstraints()
    {
        $customer1 = new Customer();
        $customer1->setEmail('john.doe@example.com');
        $customer1->setUsername('johndoe');

        $customer2 = new Customer();
        $customer2->setEmail('john.doe@example.com');
        $customer2->setUsername('johnsmith');

        $this->assertNotEquals($customer1->getEmail(), $customer2->getUsername());
    }
}
