<?php

namespace App\Repositories\Eloquent;

use App\Models\BaseModel;
use App\Models\CompanyEmployee;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CompanyEmployeeRepository extends BaseRepository {

    public function __construct() {

    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): CompanyEmployee {
        return CompanyEmployee::create($parameters);
    }

    public function store(BaseModel $model): bool {
        return $model->store();
    }

    public function update(BaseModel $model, array $parameters): bool {
        return $model->update($parameters);
    }

    public function destroy(int $id): bool {
        if ($this->find($id) === null) {
            return false;
        }
        return CompanyEmployee::destroy($id) !== null;
    }

    public function find(int $id): ?Model {
        return CompanyEmployee::view($id);
    }

    /**
     * @throws Exception
     */
    public function linkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        if ($this->findCompanyEmployee($companyId, $employeeId) !== null) {
            throw new Exception("Company [{$companyId}] already has linked employee [{$employeeId}].", 422);
        }
        $relation = new CompanyEmployee;
        $relation->company_id = $companyId;
        $relation->employee_id = $employeeId;
        return $save === true ? $relation->save() : $relation;
    }

    public function findCompanyEmployee(int $companyId, int $employeeId, bool $save = true): ?CompanyEmployee {
        return CompanyEmployee::where('company_id', '=', $companyId)->where('employee_id', '=', $employeeId)->get()->first();
    }

    public function get(array $filters): Collection {
        return $this->filter()->get();
    }

    public function filter(array $parameters = []): Builder {
        return CompanyEmployee::filter($parameters);
    }

    public function unlinkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        $relation = $this->findCompanyEmployee($companyId, $employeeId);
        if ($relation === null) {
            throw new ModelNotFoundException("Relation for company [{$companyId}], employee [{$employeeId}] doesn't exist.", 404);
        }
        return $save === true ? $relation->delete() : $relation;
    }

    public function transform(BaseModel $model): array {
        return $model->toArray();
    }
}
