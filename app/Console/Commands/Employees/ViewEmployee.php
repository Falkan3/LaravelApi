<?php

namespace App\Console\Commands\Employees;

use App\Services\EmployeeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ViewEmployee extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:employee:view {id}';
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

        $employee = $this->employeeService->find($id);

        if ($employee === null) {
            $this->handleError(new ModelNotFoundException("Employee [$id] doesn't exist.", 404));
            return;
        }

        $this->info("Employee [{$id}]:");
        foreach ($employee->toArray() as $column => $value) {
            $this->info("{$column}: {$value}");
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while viewing employee: ' . $e->getMessage());
    }
}
