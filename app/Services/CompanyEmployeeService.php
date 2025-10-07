<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\CompanyEmployee;
use App\Repositories\Eloquent\CompanyEmployeeRepository;
use App\Repositories\Eloquent\CompanyRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CompanyEmployeeService extends BaseService {
    private CompanyEmployeeRepository $companyEmployeeRepository;
    private CompanyRepository $companyRepository;
    private EmployeeRepository $employeeRepository;

    public function __construct(CompanyEmployeeRepository $companyEmployeeRepository, CompanyRepository $companyRepository, EmployeeRepository $employeeRepository) {
        $this->companyEmployeeRepository = $companyEmployeeRepository;
        $this->companyRepository = $companyRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function get(array $parameters): Collection {
        return $this->companyEmployeeRepository->get($parameters);
    }

    public function filter(array $parameters = []): Builder {
        return $this->companyEmployeeRepository->filter($parameters);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): CompanyEmployee {
        return $this->companyEmployeeRepository->create($parameters);
    }

    public function store(BaseModel $model): bool {
        return $this->companyEmployeeRepository->store($model);
    }

    public function update(BaseModel $model, array $parameters): bool {
        return $this->companyEmployeeRepository->update($model, $parameters);
    }

    public function destroy(int $id): bool {
        return $this->companyEmployeeRepository->destroy($id);
    }

    public function findCompanyEmployee(int $companyId, int $employeeId): ?BaseModel {
        return $this->companyEmployeeRepository->findCompanyEmployee($companyId, $employeeId);
    }

    /**
     * @throws Exception
     */
    public function linkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        if ($this->companyRepository->find($companyId) === null) {
            throw new ModelNotFoundException("Company [{$companyId}] doesn't exist.", 404);
        }
        if ($this->employeeRepository->find($employeeId) === null) {
            throw new ModelNotFoundException("Employee [{$employeeId}] doesn't exist.", 404);
        }
        return $this->companyEmployeeRepository->linkEmployee($companyId, $employeeId, $save);
    }

    public function find(int $id): ?BaseModel {
        return $this->companyEmployeeRepository->find($id);
    }

    public function unlinkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        if ($this->companyRepository->find($companyId) === null) {
            throw new ModelNotFoundException("Company [{$companyId}] doesn't exist.", 404);
        }
        if ($this->employeeRepository->find($employeeId) === null) {
            throw new ModelNotFoundException("Employee [{$employeeId}] doesn't exist.", 404);
        }
        return $this->companyEmployeeRepository->unlinkEmployee($companyId, $employeeId, $save);
    }

    public function transform(BaseModel $model) {
        // todo: call transformer
        // return (new CompanyTransformer)->transform($model);
    }
}
