<?php

namespace App\Console\Commands\Employees;

use App\Services\EmployeeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyEmployee extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:employee:destroy {id}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService) {
        parent::__construct();

        $this->employeeService = $employeeService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $id = $this->argument('id');

        $destroyResponse = $this->employeeService->destroy($id);

        if ($destroyResponse === false) {
            $this->handleError(new ModelNotFoundException("Employee [$id] doesn't exist.", 404));
            return;
        }

        $this->info("Employee [{$id}] destroyed successfully.");
    }

    public function handleError(Exception $e): void {
        $this->error('Error while destroying a employee: ' . $e->getMessage());
    }
}
