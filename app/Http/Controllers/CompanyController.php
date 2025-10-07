<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyCreateRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Requests\LinkCompanyEmployeeRequest;
use App\Services\CompanyEmployeeService;
use App\Services\CompanyService;
use App\Services\EmployeeService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;

class CompanyController extends Controller {
    protected CompanyService $companyService;
    protected EmployeeService $employeeService;
    protected CompanyEmployeeService $companyEmployeeService;

    /**
     * CompanyController constructor.
     * The repository is injected here, matching your constructor.
     */
    public function __construct(CompanyService $companyService, EmployeeService $employeeService, CompanyEmployeeService $companyEmployeeService) {
        $this->companyService = $companyService;
        $this->$employeeService = $employeeService;
        $this->companyEmployeeService = $companyEmployeeService;
    }

    /**
     * GET /api/companies
     * Corresponds to $this->companyService->get($parameters) and filter($parameters).
     */
    public function index(Request $request): JsonResponse {
        $parameters = [
            'id' => $request->query('id')
        ];

        $companies = $this->companyService->get($parameters);

        return response()->json($companies);
    }

    /**
     * GET /api/companies/{id}
     * Corresponds to $this->companyService->find($id).
     */
    public function show(int $id): JsonResponse {
        $company = $this->companyService->find($id);

        if ($company === null) {
            return response()->json(['message' => 'Company not found.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($company);
    }

    /**
     * POST /api/companies
     * Corresponds to $this->companyService->create($parameters).
     */
    public function create(CompanyCreateRequest $request): JsonResponse {
        try {
            // The create method handles the instantiation and saving of the model
            $employee = $this->companyService->create($request->validated());

            // Ensure the service method calls are correctly updated
            $storeSuccess = $this->companyService->store($employee);

            if ($storeSuccess === false) {
                return response()->json(['message' => 'Failed to store created company.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json($employee, Response::HTTP_CREATED);
        } catch (Exception $e) {
            // Handle service/database exceptions
            return response()->json([
                'message' => 'Failed to create company.',
                'error'   => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT/PATCH /api/companies/{id}
     * Corresponds to $this->companyService->update($model, $parameters).
     * Assumes a CompanyUpdateRequest for validation.
     */
    public function update(CompanyUpdateRequest $request, int $id): JsonResponse {
        $company = $this->companyService->find($id);

        if ($company === null) {
            return response()->json(['message' => 'Company not found.'], Response::HTTP_NOT_FOUND);
        }

        try {
            // The update method handles applying parameters and saving
            $updatedCompany = $this->companyService->update($company, $request->validated());
            $storeSuccess = $this->companyService->store($company);

            if ($storeSuccess === false) {
                return response()->json(['message' => 'Failed to store updated company.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json($updatedCompany);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update company.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/companies/{id}
     * Corresponds to $this->companyService->destroy($id).
     */
    public function destroy(int $id): JsonResponse {
        $destroySuccess = $this->companyService->destroy($id);

        if ($destroySuccess === false) {
            // Check if it failed because the model wasn't found
            if ($this->companyService->find($id) === null) {
                return response()->json(['message' => 'Company not found.'], Response::HTTP_NOT_FOUND);
            }
            // Or if deletion failed for another reason
            return response()->json(['message' => 'Failed to delete company.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * POST /api/companies/link-employee
     * Links an existing employee to an existing company.
     */
    public function linkEmployee(LinkCompanyEmployeeRequest $request): JsonResponse {
        $companyId = $request->post('company_id');
        $employeeId = $request->post('employee_id');

        try {
            // 1. Validate existence
            $company = $this->companyService->find($companyId);
            if ($company === null) {
                throw new ModelNotFoundException("Company [$companyId] not found.", Response::HTTP_NOT_FOUND);
            }

            $employee = $this->employeeService->find($employeeId);
            if ($employee === null) {
                throw new ModelNotFoundException("Employee [$employeeId] not found.", Response::HTTP_NOT_FOUND);
            }

            // 2. Execute the linking logic from the service
            $isLinked = $this->companyEmployeeService->linkEmployee($companyId, $employeeId);

            if ($isLinked === false) {
                // If the link operation fails for a non-existence reason (e.g., database constraint)
                return response()->json([
                    'message' => "Failed to link employee [{$employeeId}] to company [{$companyId}].",
                ], Response::HTTP_CONFLICT);
            }

            // 3. Success response (200 OK or 201 Created if resource was truly created, 204 No Content often used for relationship modifications)
            return response()->json([
                'message'     => "Successfully linked employee [{$employeeId}] to company [{$companyId}].",
                'company_id'  => $companyId,
                'employee_id' => $employeeId,
            ], Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $e) {
            // Handle 404
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            // Handle general errors
            return response()->json(['message' => 'An unexpected error occurred during linking.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/companies/unlink-employee
     * Unlinks an existing employee from an existing company.
     */
    public function unlinkEmployee(LinkCompanyEmployeeRequest $request): JsonResponse {
        $companyId = $request->post('company_id');
        $employeeId = $request->post('employee_id');

        try {
            // 1. Validate existence
            $company = $this->companyService->find($companyId);
            if ($company === null) {
                throw new ModelNotFoundException("Company [$companyId] not found.", Response::HTTP_NOT_FOUND);
            }

            $employee = $this->employeeService->find($employeeId);
            if ($employee === null) {
                throw new ModelNotFoundException("Employee [$employeeId] not found.", Response::HTTP_NOT_FOUND);
            }

            // 2. Check if the employee is linked to the company
            $isLinked = $this->companyEmployeeService->findCompanyEmployee($companyId, $employeeId);

            if ($isLinked === null) {
                // Link doesn't exist
                return response()->json([
                    'message' => "Employee [{$employeeId}] is not linked to company [{$companyId}].",
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // 3. Unlink the employee
            $unlinked = $this->companyEmployeeService->unlinkEmployee($companyId, $employeeId);

            if ($unlinked === false) {
                // Error while unlinking
                return response()->json([
                    'message' => "An unexpected error occurred during unlinking.",
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // 4. Success response (200 OK or 201 Created if resource was truly created, 204 No Content often used for relationship modifications)
            return response()->json([
                'message'     => "Successfully unlinked employee [{$employeeId}] to company [{$companyId}].",
                'company_id'  => $companyId,
                'employee_id' => $employeeId,
            ], Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $e) {
            // Handle 404
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            // Handle general errors
            return response()->json(['message' => 'An unexpected error occurred during unlinking.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
