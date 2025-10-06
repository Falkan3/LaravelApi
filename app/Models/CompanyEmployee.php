<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyEmployee extends Model {
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['company_id', 'employee_id'];

    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }
}
