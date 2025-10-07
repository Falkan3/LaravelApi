<?php

namespace App\Console\Commands\Employees;

use App\Services\EmployeeService;
use Exception;
use Illuminate\Console\Command;

class CreateEmployee extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:employee:create {first_name} {last_name} {email} {phone_number?}';
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
        $firstName = $this->argument('first_name');
        $lastName = $this->argument('last_name');
        $email = $this->argument('email');
        $phoneNumber = $this->argument('phone_number');

        $parameters = [
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'email'        => $email,
            'phone_number' => $phoneNumber,
        ];

        try {
            $company = $this->employeeService->create($parameters);
            $company->store();
        } catch (Exception $e) {
            $this->handleError($e);
            return;
        }

        $this->info("Successfully created a new employee [{$company->getKey()}].");
        foreach ($company->toArray() as $column => $value) {
            $this->info("{$column}: {$value}");
        }
    }

    public function handleError(Exception $e): void {
        $this->error('Error while creating a new employee: ' . $e->getMessage());
    }
}
