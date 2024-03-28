<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Illuminate\Support\Facades\Auth;

class EmployeeCategory extends Model
{
    use HasFactory;
    protected $table = 'employees_categories';
    protected $fillable = ['name', 'description'];
    public function getDescriptionAttribute($description)
    {
        // Limit the description to 150 characters
        return \Str::limit($description, 50, '...');
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        self::creating(function ($model) {
            $model->user_created = Auth::id();
        });
        self::updating(function ($model) {
            $model->user_updated = Auth::id();
        });
    }
}
