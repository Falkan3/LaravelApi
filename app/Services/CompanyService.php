<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Company;
use App\Models\CompanyEmployee;
use App\Models\Employee;
use App\Repositories\Eloquent\CompanyRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CompanyService extends BaseService {
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository) {
        $this->companyRepository = $companyRepository;
    }

    public function get(array $parameters): Collection {
        return $this->companyRepository->get($parameters);
    }

    public function find(int $id): ?BaseModel {
        return $this->companyRepository->find($id);
    }

    public function filter(array $parameters = []): Builder {
        return $this->companyRepository->filter($parameters);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): Company {
        return $this->companyRepository->create($parameters);
    }

    public function store(BaseModel $model): bool {
        return $this->companyRepository->store($model);
    }

    public function update(BaseModel $model, array $parameters): bool {
        return $this->companyRepository->update($model, $parameters);
    }

    public function destroy(int $id): bool {
        return $this->companyRepository->destroy($id);
    }

    public function linkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        return $this->companyRepository->linkEmployee($companyId, $employeeId, $save);
    }

    public function unlinkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        return $this->companyRepository->unlinkEmployee($companyId, $employeeId, $save);
    }

    public function transform(BaseModel $model) {
        // todo: call transformer
        // return (new CompanyTransformer)->transform($model);
    }
}
