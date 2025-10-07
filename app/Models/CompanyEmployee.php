<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CompanyEmployee extends BaseModel {
    protected $table = 'company_employee';
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['company_id', 'employee_id'];

    public static function view(int $id): ?Model {
        return self::filter(['id' => $id])->get()->first();
    }

    public static function filter(array $parameters = []): Builder {
        $query = self::query();

        if (array_key_exists('id', $parameters)) {
            $query->where('id', '=', $parameters['id']);
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

    private static function getRules(bool $update = false): array {
        if ($update === false) {
            return [
                'company_id'  => ['required', 'int'],
                'employee_id' => ['required', 'int'],
            ];
        }

        return [
            'company_id'  => ['nullable', 'int'],
            'employee_id' => ['nullable', 'int'],
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

    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
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
