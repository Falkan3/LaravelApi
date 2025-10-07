<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Employee;
use App\Repositories\Eloquent\EmployeeRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class EmployeeService extends BaseService {
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository) {
        $this->employeeRepository = $employeeRepository;
    }

    public function get(array $parameters): Collection {
        return $this->employeeRepository->get($parameters);
    }

    public function find(int $id): ?BaseModel {
        return $this->employeeRepository->find($id);
    }

    public function filter(array $parameters = []): Builder {
        return $this->employeeRepository->filter($parameters);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): Employee {
        return $this->employeeRepository->create($parameters);
    }

    public function store(BaseModel $model): bool {
        return $this->employeeRepository->store($model);
    }

    public function update(BaseModel $model, array $parameters): Employee {
        return $this->employeeRepository->update($model, $parameters);
    }

    public function destroy(int $id): bool {
        return $this->employeeRepository->destroy($id);
    }

    public function transform(BaseModel $model) {
        // todo: call transformer
        // return (new EmployeeTransformer)->transform($model);
    }
}
