<?php

namespace App\Services;

use App\Models\BaseModel;

abstract class BaseService {
    public function find(int $id): ?BaseModel {
        return $this->get(['id' => $id])->first();
    }

    public abstract function get(array $parameters);

    public abstract function create(array $parameters);

    public abstract function store(BaseModel $model);

    public abstract function update(BaseModel $model, array $parameters);

    public abstract function destroy(int $id);

    public abstract function transform(BaseModel $model);
}
