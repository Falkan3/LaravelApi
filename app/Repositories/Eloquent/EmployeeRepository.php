<?php

namespace App\Repositories\Eloquent;

use App\Models\BaseModel;
use App\Models\Employee;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EmployeeRepository extends BaseRepository {

    public function __construct() {

    }

    public function get(array $parameters): Collection {
        return Employee::list($parameters);
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

    public function update(BaseModel $model, array $parameters): Employee {
        return $model->edit($parameters);
    }

    public function destroy(int $id): bool {
        if ($this->find($id) === null) {
            return false;
        }
        return Employee::destroy($id) !== null;
    }

    public function find(int $id): ?Model {
        return Employee::view($id);
    }

    public function transform(BaseModel $model): array {
        return $model->toArray();
    }
}
