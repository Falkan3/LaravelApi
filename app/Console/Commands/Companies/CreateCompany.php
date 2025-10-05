<?php

namespace App\Console\Commands\Companies;

use App\Services\CompanyService;
use Exception;
use Illuminate\Console\Command;

class CreateCompany extends Command {
    private CompanyService $companyService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:company:create {name} {tax_id} {city} {address} {post_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(CompanyService $companyService) {
        parent::__construct();

        $this->companyService = $companyService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $name = $this->argument('name');
        $taxId = $this->argument('tax_id');
        $city = $this->argument('city');
        $address = $this->argument('address');
        $postCode = $this->argument('post_code');

        $parameters = [
            'name'      => $name,
            'tax_id'    => $taxId,
            'city'      => $city,
            'address'   => $address,
            'post_code' => $postCode,
        ];

        try {
            $company = $this->companyService->create($parameters);
            $company->store();
        } catch (Exception $e) {
            $this->handleError($e);
            return;
        }

        $this->info("Successfully created a new company [{$company->getKey()}].");
        foreach ($company->toArray() as $column => $value) {
            $this->info("{$column}: {$value}");
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while creating a new company: ' . $e->getMessage());
    }
}
