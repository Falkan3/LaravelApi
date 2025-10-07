<?php

namespace App\Console\Commands\Companies;

use App\Services\CompanyService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateCompany extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:company:update {id} {--name=} {--tax_id=} {--city=} {--address=} {--post_code=}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService) {
        parent::__construct();

        $this->companyService = $companyService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $id = $this->argument('id');
        $name = $this->option('name');
        $taxId = $this->option('tax_id');
        $city = $this->option('city');
        $address = $this->option('address');
        $postCode = $this->option('post_code');

        $parameters = [
            'name'      => $name,
            'tax_id'    => $taxId,
            'city'      => $city,
            'address'   => $address,
            'post_code' => $postCode,
        ];
        $parameters = array_filter($parameters, fn($val) => $val !== null);

        $company = $this->companyService->find($id);

        if ($company === null) {
            $this->handleError(new ModelNotFoundException("Company [$id] doesn't exist.", 404));
            return;
        }

        try {
            $this->companyService->update($company, $parameters);
        } catch (Exception $e) {
            $this->handleError($e);
            return;
        }

        if ($this->companyService->store($company) === false) {
            $this->handleError(new Exception("Company [$id] could not be updated.", 500));
            return;
        }

        $this->info("Company [{$id}] updated successfully.");

        foreach ($company->toArray() as $column => $value) {
            $this->info("{$column}: {$value}");
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while updating company: ' . $e->getMessage());
    }
}
