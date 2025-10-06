<?php

namespace App\Console\Commands\Employees;

use App\Services\EmployeeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateEmployee extends Command {
    private EmployeeService $employeeService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:employee:update {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(EmployeeService $employeeService) {
        parent::__construct();

        $this->employeeService = $employeeService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $id = $this->argument('id');

        $employee = $this->employeeService->find($id);

        if ($employee === null) {
            $this->handleError(new ModelNotFoundException("Employee [$id] doesn't exist.", 404));
            return;
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while updating employee: ' . $e->getMessage());
    }
}
