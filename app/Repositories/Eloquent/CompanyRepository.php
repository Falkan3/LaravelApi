<?php

namespace App\Repositories\Eloquent;

use App\Models\BaseModel;
use App\Models\Company;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CompanyRepository extends BaseRepository {

    public function __construct() {

    }

    public function get(array $filters): Collection {
        return $this->filter()->get();
    }

    public function find(int $id): BaseModel {
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
        return Company::destroy($id) !== null;
    }

    public function transform(BaseModel $model): array {
        return $model->toArray();
    }
}
