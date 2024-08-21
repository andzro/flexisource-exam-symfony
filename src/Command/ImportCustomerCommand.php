<?php

namespace App\Command;

use App\Service\CustomerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import-customer',
    description: 'Imports customers from an external API',
    hidden: false,
    aliases: ['app:import']
)]
class ImportCustomerCommand extends Command
{
    private CustomerService $customerService;
    private string $importCustomerUrl;

    public function __construct(CustomerService $customerService, ParameterBagInterface $params)
    {
        parent::__construct();
        $this->customerService = $customerService;
        $this->importCustomerUrl = $params->get('import_customer_url');
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::OPTIONAL, 'The URL to fetch user data from');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = $input->getArgument('url') ?? $this->importCustomerUrl;

        try {
            $this->customerService->importCustomers($url);
            $output->writeln('<info>Users imported successfully!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Failed to import users: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
