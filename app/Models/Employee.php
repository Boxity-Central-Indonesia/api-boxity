<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Employee extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'company_id',
        'job_title',
        'date_of_birth',
        'employment_status',
        'hire_date',
        'termination_date',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone_number',
        'notes',
        'department_id',
        'category_id'
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        self::creating(function ($model) {
            $model->user_created = auth()->id();
        });
        self::updating(function ($model) {
            $model->user_updated = auth()->id();
        });
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(CompaniesDepartment::class);
    }
    public function category()
    {
        return $this->belongsTo(EmployeeCategory::class, 'category_id');
    }
}
