<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class ProductsCategory extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name', 'image',
        'description', 'type'
    ];
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
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function getDescriptionAttribute($description)
    {
        // Limit the description to 150 characters
        return \Str::limit($description, 50, '...');
    }
}