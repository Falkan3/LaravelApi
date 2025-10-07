<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Services\EmployeeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;

class EmployeeController extends Controller {
    protected EmployeeService $employeeService;

    /**
     * EmployeeController constructor.
     * The service is injected here, matching your constructor pattern.
     */
    public function __construct(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     * GET /api/employees
     * Corresponds to $this->employeeService->get($parameters) and filter($parameters).
     */
    public function index(Request $request): JsonResponse {
        $parameters = [
            'id' => $request->query('id')
        ];

        $employees = $this->employeeService->get($parameters);

        return response()->json($employees);
    }

    /**
     * GET /api/employees/{id}
     * Corresponds to $this->employeeService->find($id).
     */
    public function show(int $id): JsonResponse {
        $employee = $this->employeeService->find($id);

        if ($employee === null) {
            return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($employee);
    }

    /**
     * POST /api/employees
     * Corresponds to $this->employeeService->create($parameters).
     */
    public function create(EmployeeCreateRequest $request): JsonResponse {
        try {
            // The create method handles the instantiation and saving of the model
            $employee = $this->employeeService->create($request->validated());

            // Ensure the service method calls are correctly updated
            $storeSuccess = $this->employeeService->store($employee);

            if ($storeSuccess === false) {
                return response()->json(['message' => 'Failed to store created employee.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json($employee, Response::HTTP_CREATED);
        } catch (Exception $e) {
            // Handle service/database exceptions
            return response()->json([
                'message' => 'Failed to create employee.',
                'error'   => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT/PATCH /api/employees/{id}
     * Corresponds to $this->employeeService->update($model, $parameters).
     * Assumes an EmployeeUpdateRequest for validation.
     */
    public function update(EmployeeUpdateRequest $request, int $id): JsonResponse {
        $employee = $this->employeeService->find($id);

        if ($employee === null) {
            return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
        }

        try {
            // The update method handles applying parameters and saving
            $updatedEmployee = $this->employeeService->update($employee, $request->validated());

            // Ensure the service method calls are correctly updated
            $storeSuccess = $this->employeeService->store($employee);

            if ($storeSuccess === false) {
                return response()->json(['message' => 'Failed to store updated employee.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json($updatedEmployee);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update employee.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/employees/{id}
     * Corresponds to $this->employeeService->destroy($id).
     */
    public function destroy(int $id): JsonResponse {
        $destroySuccess = $this->employeeService->destroy($id);

        if ($destroySuccess === false) {
            // Check if it failed because the model wasn't found
            if ($this->employeeService->find($id) === null) {
                return response()->json(['message' => 'Employee not found.'], Response::HTTP_NOT_FOUND);
            }
            // Or if deletion failed for another reason
            return response()->json(['message' => 'Failed to delete employee.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
