<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'job_title_category_id'
    ];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->user_created = Auth::id();
        });
        self::updating(function ($model) {
            $model->user_updated = Auth::id();
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
}
