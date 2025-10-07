<?php

namespace App\Console\Commands\Companies;

use App\Services\CompanyService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ViewCompany extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:company:view {id}';
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

        $company = $this->companyService->find($id);

        if ($company === null) {
            $this->handleError(new ModelNotFoundException("Company [$id] doesn't exist.", 404));
            return;
        }

        $this->info("Company [{$id}]:");
        foreach ($company->toArray() as $column => $value) {
            $this->info("{$column}: {$value}");
        }

        // relations
        if ($company->employees->isNotEmpty()) {
            $this->info("Employees ({$company->employees->count()}):");
            foreach ($company->employees as $employee) {
                foreach ($employee->toArray() as $column => $value) {
                    $this->info("{$column}: " . $value);
                }
            }
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while viewing company: ' . $e->getMessage());
    }
}
