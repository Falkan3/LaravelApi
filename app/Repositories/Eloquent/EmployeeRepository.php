<?php

namespace App\Repositories\Eloquent;

use App\Models\BaseModel;
use App\Models\Employee;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class EmployeeRepository extends BaseRepository {

    public function __construct() {

    }

    public function get(array $parameters): Collection {
        return Employee::list($parameters);
    }

    public function find(int $id): BaseModel {
        return Employee::view($id);
    }

    public function filter(array $parameters = []): Builder {
        return Employee::filter($parameters);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): Employee {
        return Employee::create($parameters);
    }

    public function store(BaseModel $model): bool {
        return $model->store();
    }

    public function update(BaseModel $model, array $parameters): bool {
        return $model->update($parameters);
    }

    public function destroy(int $id): bool {
        return Employee::destroy($id) !== null;
    }

    public function transform(BaseModel $model): array {
        return $model->toArray();
    }
}
