<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Employee extends BaseModel {
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['first_name', 'last_name', 'email', 'phone_number'];

    protected array $normalizedFields = ['first_name', 'last_name'];

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
                'first_name'   => ['required', 'string', 'max:100'],
                'last_name'    => ['required', 'string', 'max:100'],
                'email'        => ['required', 'string', 'email', 'max:256', 'unique:employees,email'],
                'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^(\+\d{1,3}\s?)?(\(?\d{1,4}\)?[\s\.\-]?\d{1,4}[\s\.\-]?\d{1,4}[\s\.\-]?\d{1,4}[\s\.\-]?\d{1,4})$/'],
            ];
        }

        return [
            'first_name'   => ['nullable', 'string', 'max:100'],
            'last_name'    => ['nullable', 'string', 'max:100'],
            'email'        => ['nullable', 'string', 'email', 'max:256', 'unique:employees,email'],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^(\+\d{1,3}\s?)?(\(?\d{1,4}\)?[\s\.\-]?\d{1,4}[\s\.\-]?\d{1,4}[\s\.\-]?\d{1,4}[\s\.\-]?\d{1,4})$/'],
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

    public static function view(int $id): ?Model {
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
