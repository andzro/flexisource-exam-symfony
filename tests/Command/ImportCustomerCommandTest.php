<?php

namespace App\Tests\Command;

use App\Command\ImportCustomerCommand;
use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Console\Command\Command;

class ImportCustomerCommandTest extends KernelTestCase
{
    private $commandTester;
    private $customerService;

    protected function setUp(): void
    {
        $this->customerService = $this->createMock(CustomerService::class);
        $params = $this->createMock(ParameterBagInterface::class);
        $params->method('get')->willReturn('http://example.com/customers');

        $command = new ImportCustomerCommand($this->customerService, $params);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteSuccessfulImport()
    {
        $this->customerService
            ->expects($this->once())
            ->method('importCustomers')
            ->with('http://example.com/customers');

        $this->commandTester->execute([
            'url' => 'http://example.com/customers',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Users imported successfully!', $output);
        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteSuccessfulImportWithDefaultUrl()
    {
        $this->customerService
            ->expects($this->once())
            ->method('importCustomers')
            ->with('http://example.com/customers');

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Users imported successfully!', $output);
        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteFailedImport()
    {
        $this->customerService
            ->expects($this->once())
            ->method('importCustomers')
            ->willThrowException(new \Exception('Error importing customers'));

        $this->commandTester->execute([
            'url' => 'http://example.com/customers',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Failed to import users: Error importing customers', $output);
        $this->assertEquals(1, $this->commandTester->getStatusCode());
    }
}
