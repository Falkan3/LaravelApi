<?php

namespace App\Console\Commands\Employees;

use App\Services\EmployeeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateEmployee extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:employee:update {id} {--first_name=} {--last_name=} {--email=} {--phone_number=}';
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
        $firstName = $this->option('first_name');
        $lastName = $this->option('last_name');
        $email = $this->option('email');
        $phoneNumber = $this->option('phone_number');

        $parameters = [
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'email'        => $email,
            'phone_number' => $phoneNumber,
        ];
        $parameters = array_filter($parameters, fn($val) => $val !== null);

        $employee = $this->employeeService->find($id);

        if ($employee === null) {
            $this->handleError(new ModelNotFoundException("Employee [$id] doesn't exist.", 404));
            return;
        }

        try {
            $this->employeeService->update($employee, $parameters);
        } catch (Exception $e) {
            $this->handleError($e);
            return;
        }

        if ($this->employeeService->store($employee) === false) {
            $this->handleError(new Exception("Employee [$id] could not be updated.", 500));
            return;
        }

        $this->info("Employee [{$id}] updated successfully.");

        foreach ($employee->toArray() as $column => $value) {
            $this->info("{$column}: {$value}");
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while updating employee: ' . $e->getMessage());
    }
}
