<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
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
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        static::creating(function ($model) {
            $model->user_created = auth()->id();
            $model->user_updated = auth()->id();
        });

        static::updating(function ($model) {
            $model->user_updated = auth()->id();
        });
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
