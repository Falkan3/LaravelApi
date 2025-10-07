<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Company extends BaseModel {
    // protected $table = 'companies';

    // todo: add country code

    protected $hidden = ['id', 'created_at', 'updated_at', 'pivot'];
    protected $fillable = ['name', 'tax_id', 'tax_id_country_code', 'tax_id_number', 'city', 'city_normalized', 'address', 'address_normalized', 'post_code', 'post_code_normalized'];
    protected array $normalizedFields = ['name', 'city', 'address', 'post_code'];

    protected ?array $createRules = null;
    protected ?array $updateRules = null;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public static function view(int $id): ?Model {
        return self::filter(['id' => $id])->get()->first();
    }

    public static function filter(array $parameters = []): Builder {
        $query = self::query();

        if (array_key_exists('id', $parameters)) {
            $query->where('id', '=', $parameters['id']);
        }

        if (array_key_exists('tax_id', $parameters)) {
            $query->where('tax_id', '=', $parameters['tax_id']);
        }

        return $query;
    }

    public static function list(array $parameters = []): Collection {
        return self::filter($parameters)->get();
    }

    /**
     * @throws ValidationException
     */
    public static function create(array $parameters): static {
        $instance = new self;

        $instance->fillRules();

        self::validate($parameters);

        $instance->fill($parameters);

        $postCode = $instance->getAttributeValue('post_code');
        if ($postCode !== null) {
            $instance->setAttribute('post_code', $instance->normalizeString($postCode));
        }

        $instance->normalize();

        return $instance;
    }

    private function fillRules(): void {
        if ($this->createRules !== null || $this->updateRules !== null) {
            return;
        }

        $this->createRules = self::getRules();
        $this->updateRules = self::getRules(true);
    }

    public static function getRules(bool $update = false): array {
        if ($update === false) {
            return [
                'name'                => ['required', 'string', 'max:256', 'min:2'],
                'tax_id'              => ['required', 'string', 'max:50', 'unique:companies,tax_id'], // todo: add validation based on country
                'tax_id_country_code' => ['nullable', 'string', 'size:3'],
                'city'                => ['required', 'string', 'max:50'],
                'address'             => ['required', 'string', 'max:256'],
                'post_code'           => ['required', 'string', 'max:20'], // For a fully-fledged solution and multi-country support, use a package like axlon/laravel-postal-code-validation which provides a postal_code rule that can validate against specific countries
            ];
        }

        return [
            'name'                => ['nullable', 'string', 'max:256', 'min:2'],
            'tax_id'              => ['nullable', 'string', 'max:50', 'unique:companies,tax_id'],
            'tax_id_country_code' => ['nullable', 'string', 'size:3'],
            'city'                => ['nullable', 'string', 'max:50'],
            'address'             => ['nullable', 'string', 'max:256'],
            'post_code'           => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public static function validate(array $parameters, bool $update = false): array {
        (new self)->fillRules();

        $validator = Validator::make($parameters, self::getRules($update));

        return $validator->validate();
    }

    public function employees(): BelongsToMany {
        return $this->belongsToMany(Employee::class);
    }

    /**
     * @throws ValidationException
     */
    public function validateSelf(array $parameters, bool $update = false): array {
        $this->fillRules();

        $validator = Validator::make($parameters, $update === false ? $this->createRules : $this->updateRules);

        return $validator->validate();
    }

    public function store(): bool {
        return $this->save();
    }

    /**
     * @throws ValidationException
     */
    public function edit(array $parameters): BaseModel {
        $this->fillRules();

        $this->validate($parameters, true);

        $this->fill($parameters);

        $postCode = $this->getAttributeValue('post_code');
        if ($postCode !== null) {
            $this->setAttribute('post_code', $this->normalizeString($postCode));
        }

        $this->normalize();

        return $this;
    }
}
