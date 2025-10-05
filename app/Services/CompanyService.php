<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Company;
use App\Repositories\Eloquent\CompanyRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CompanyService extends BaseService {
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository) {
        $this->companyRepository = $companyRepository;
    }

    public function get(array $parameters): Collection {
        return $this->filter($parameters);
    }

    public function find(array $parameters): ?BaseModel {
        return $this->get($parameters)->first();
    }

    public function filter(array $parameters = []): Collection {
        return $this->companyRepository->filter($parameters);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $parameters): Company {
        return $this->companyRepository->create($parameters);
    }

    public function store(BaseModel $model) {

    }

    public function update(BaseModel $model, array $parameters) {

    }

    public function destroy(int $id) {

    }

    public function transform(BaseModel $model) {

    }
}
