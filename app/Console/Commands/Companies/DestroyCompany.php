<?php

namespace App\Console\Commands\Companies;

use App\Services\CompanyService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyCompany extends Command {
    private CompanyService $companyService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:company:destroy {id}';

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

        $destroyResponse = $this->companyService->destroy($id);

        if ($destroyResponse === false) {
            $this->handleError(new ModelNotFoundException("Company [$id] doesn't exist.", 404));
            return;
        }

        $this->info("Company [{$id}] destroyed successfully.");
    }

    public function handleError(Exception $e): void {
        $this->error('Error while destroying a company: ' . $e->getMessage());
    }
}
