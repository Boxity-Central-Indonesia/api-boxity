<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class CompaniesDepartment extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'responsibilities',
        'company_id',
    ];
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->user_created = Auth::id();
            $model->user_updated = Auth::id();
        });

        static::updating(function ($model) {
            $model->user_updated = Auth::id();
        });
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
