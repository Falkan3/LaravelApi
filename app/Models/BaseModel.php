<?php

namespace App\Models;

use App\Helpers\Formatters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model {
    protected array $normalizedFields = [];
    protected ?array $createRules = null;
    protected ?array $updateRules = null;

    public abstract static function validate(array $parameters): array;

    public abstract static function view(int $id): ?Model;

    public abstract static function list(array $parameters = []): Collection;

    public abstract static function filter(array $parameters = []): Builder;

    public abstract static function create(array $parameters);

    // public abstract static function destroy(int $id): bool;

    public abstract function validateSelf(array $parameters): array;

    public abstract function store(): bool;

    public abstract function edit(array $parameters);

    // public abstract function destroySelf(): bool;

    protected function normalize(): void {
        foreach ($this->normalizedFields as $field) {
            $this->setAttribute($field . '_normalized', Formatters::sanitize_full_text_search($this->getAttributeValue($field)));
        }
    }
}
