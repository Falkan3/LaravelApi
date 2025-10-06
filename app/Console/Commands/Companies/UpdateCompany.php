<?php

namespace App\Console\Commands\Companies;

use App\Services\CompanyService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateCompany extends Command {
    private CompanyService $companyService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:company:update {id}';

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
        $id = $this->argument('id');

        $company = $this->companyService->find($id);

        if ($company === null) {
            $this->handleError(new ModelNotFoundException("Company [$id] doesn't exist.", 404));
            return;
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while updating company: ' . $e->getMessage());
    }
}
