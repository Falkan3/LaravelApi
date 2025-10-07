<?php

namespace App\Repositories\Eloquent;

use App\Models\BaseModel;
use App\Models\Company;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CompanyRepository extends BaseRepository {

    public function __construct() {

    }

    public function get(array $filters): Collection {
        return $this->filter()->get();
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

    public function update(BaseModel $model, array $parameters): Company {
        return $model->edit($parameters);
    }

    public function destroy(int $id): bool {
        if ($this->find($id) === null) {
            return false;
        }
        return Company::destroy($id) !== null;
    }

    public function find(int $id): ?Model {
        return Company::view($id);
    }

    public function transform(BaseModel $model): array {
        return $model->toArray();
    }
}
