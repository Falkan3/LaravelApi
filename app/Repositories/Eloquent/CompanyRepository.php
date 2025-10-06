<?php

namespace App\Repositories\Eloquent;

use App\Models\BaseModel;
use App\Models\Company;
use App\Models\CompanyEmployee;
use App\Models\Employee;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CompanyRepository extends BaseRepository {

    public function __construct() {

    }

    public function get(array $filters): Collection {
        return $this->filter()->get();
    }

    public function find(int $id): ?Model {
        return Company::view($id);
    }

    public function filter(array $parameters = []): Builder {
        return Company::filter($parameters);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): Company {
        return Company::create($parameters);
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
        return Company::destroy($id) !== null;
    }

    public function linkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        $relation = new CompanyEmployee;
        $relation->company_id = $companyId;
        $relation->employee_id = $employeeId;
        return $save === true ? $relation->save() : $relation;
    }

    public function unlinkEmployee(int $companyId, int $employeeId, bool $save = true): CompanyEmployee|bool {
        $relation = $this->findEmployee($companyId, $employeeId);
        if ($relation === null) {
            throw new ModelNotFoundException("Relation for company [{$companyId}], employee [{$employeeId}] doesn't exist.", 404);
        }
        return $save === true ? $relation->save() : $relation;
    }

    public function findEmployee(int $companyId, int $employeeId, bool $save = true): ?CompanyEmployee {
        return CompanyEmployee::where('company_id', '=', $companyId)->where('employee_id', '=', $employeeId)->get()->first();
    }

    public function transform(BaseModel $model): array {
        return $model->toArray();
    }
}
