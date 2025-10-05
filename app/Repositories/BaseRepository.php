<?php

namespace App\Repositories;

use App\Helpers\Formatters;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseRepository {
    public abstract function get(array $filters): Collection;

    public abstract function filter(array $filters): Builder;

    public abstract function find(int $id): BaseModel;

    public abstract function create(array $parameters): BaseModel;

    public abstract function store(BaseModel $model): bool;

    public abstract function update(BaseModel $model, array $parameters);

    public abstract function destroy(int $id): bool;

    public abstract function transform(BaseModel $model): array;

    function isSuccessfulStatusCode($statusCode): bool {
        return $statusCode >= 200 && $statusCode < 300;
    }

    function trimString(string $input): string {
        return Formatters::trim_string($input);
    }

    function sanitizeString(string $input): string {
        return Formatters::clean_string(Formatters::trim_string($input));
    }

    function sanitizeFullTextSearch(string $input): string {
        return Formatters::sanitize_full_text_search($input);
    }

    function debugQuery(Builder $query): string {
        return Str::replaceArray('?', array_map(fn($binding) => "'$binding'", $query->getBindings()), $query->toSql());
    }

    function log(string $type, string $message, array $context = []): void {
        $formattedMessage = '[' . static::class . '] ' . $message;
        switch ($type) {
            default:
            case 'info':
                Log::info($formattedMessage, $context);
                break;
            case 'error':
                Log::error($formattedMessage, $context);
                break;
        }
    }
}
