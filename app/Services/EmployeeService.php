<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Employee;
use App\Repositories\Eloquent\EmployeeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class EmployeeService extends BaseService {
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository) {
        $this->employeeRepository = $employeeRepository;
    }

    public function get(array $parameters): Collection {
        return $this->filter($parameters);
    }

    public function find(array $parameters): ?BaseModel {
        return $this->get($parameters)->first();
    }

    public function filter(array $parameters = []): Collection {
        return $this->employeeRepository->filter($parameters);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): Employee {
        return $this->employeeRepository->create($parameters);
    }

    public function store(BaseModel $model) {

    }

    public function update(BaseModel $model, array $parameters) {

    }

    public function destroy(int $id) {

    }

    public function transform(BaseModel $model) {

    }
}
