<?php

namespace App\Console\Commands\Companies;

use App\Services\CompanyEmployeeService;
use App\Services\CompanyService;
use App\Services\EmployeeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ManageCompanyEmployee extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:company:manageEmployees {company_id} {employee_id} {action=link}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected array $actions = ['link', 'unlink'];
    private CompanyService $companyService;
    private EmployeeService $employeeService;
    private CompanyEmployeeService $companyEmployeeService;

    public function __construct(CompanyService $companyService, EmployeeService $employeeService, CompanyEmployeeService $companyEmployeeService) {
        parent::__construct();

        $this->companyService = $companyService;
        $this->employeeService = $employeeService;
        $this->companyEmployeeService = $companyEmployeeService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $companyId = $this->argument('company_id');
        $employeeId = $this->argument('employee_id');
        $action = $this->argument('action');

        if (in_array($action, $this->actions) === false) {
            $this->handleError(new Exception("Invalid action [{$action}]. Available actions: " . implode(', ', $this->actions), 422));
            return;
        }

        $company = $this->companyService->find($companyId);
        $employee = $this->employeeService->find($employeeId);

        if ($company === null) {
            $this->handleError(new ModelNotFoundException("Company [$companyId] doesn't exist.", 404));
            return;
        }

        if ($employee === null) {
            $this->handleError(new ModelNotFoundException("Employee [$employeeId] doesn't exist.", 404));
            return;
        }

        switch ($action) {
            default:
            case 'link':
                try {
                    $response = $this->companyEmployeeService->linkEmployee($companyId, $employeeId);
                } catch (Exception $e) {
                    $this->handleError($e);
                    return;
                }

                if ($response === false) {
                    $this->handleError(new ModelNotFoundException("Linking employee [{$employeeId}] to company [{$companyId}] was unsuccessful.", 500));
                    return;
                }
                $message = "Successfully linked employee [{$employeeId}] to company [{$companyId}].";
                break;
            case 'unlink':
                try {
                    $response = $this->companyEmployeeService->unlinkEmployee($companyId, $employeeId);
                } catch (Exception $e) {
                    $this->handleError($e);
                    return;
                }
                if ($response === false) {
                    $this->handleError(new ModelNotFoundException("Linking employee [{$employeeId}] to company [{$companyId}] was unsuccessful.", 500));
                    return;
                }
                $message = "Successfully unlinked employee [{$employeeId}] from company [{$companyId}].";
        }

        $this->info($message);
    }

    public function handleError(Exception $e): void {
        $this->error('Error while updating company: ' . $e->getMessage());
    }
}
