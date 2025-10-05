<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Company extends BaseModel {
    // protected $table = 'companies';

    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['name', 'tax_id', 'tax_id_country_code', 'tax_id_number', 'city', 'city_normalized', 'address', 'address_normalized', 'post_code', 'post_code_normalized'];
    protected array $normalizedFields = ['name', 'city', 'address', 'post_code'];

    protected ?array $createRules = null;
    protected ?array $updateRules = null;

    public function employees(): HasMany {
        return $this->hasMany(Employee::class);
    }

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    private function fillRules(): void {
        if ($this->createRules !== null || $this->updateRules !== null) {
            return;
        }

        $this->createRules = self::getRules();
        $this->updateRules = self::getRules(true);
    }

    private static function getRules(bool $update = false): array {
        if ($update === false) {
            return [
                'name'      => ['required', 'string', 'max:256', 'min:2'],
                'tax_id'    => ['required', 'string', 'max:50', 'unique:companies,tax_id'],
                'city'      => ['required', 'string', 'max:50'],
                'address'   => ['required', 'string', 'max:256'],
                'post_code' => ['required', 'string', 'max:20'], // For a fully-fledged solution and multi-country support, use a package like axlon/laravel-postal-code-validation which provides a postal_code rule that can validate against specific countries
            ];
        }

        return [
            'name'      => ['nullable', 'string', 'max:256', 'min:2'],
            'tax_id'    => ['nullable', 'string', 'max:50', 'unique:companies,tax_id'],
            'city'      => ['nullable', 'string', 'max:50'],
            'address'   => ['nullable', 'string', 'max:256'],
            'post_code' => ['nullable', 'string', 'max:20'],
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

    public static function view(int $id): BaseModel {
        return self::filter(['id' => $id])->get()->first();
    }

    public static function list(array $parameters = []): Collection {
        return self::filter($parameters)->get();
    }

    public static function filter(array $parameters = []): Builder {
        $query = self::query();

        if (array_key_exists('id', $parameters)) {
            $query->where('id', '=', $parameters['id']);
        }

        return $query;
    }

    /**
     * @throws ValidationException
     */
    public static function create(array $parameters): static {
        $instance = new self;

        $instance->fillRules();

        self::validate($parameters);

        $instance->fill($parameters);

        $instance->normalize();

        return $instance;
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

        $this->validate($parameters);

        $this->fill($parameters);

        $this->normalize();

        return $this;
    }
}
