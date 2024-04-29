<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Warehouse extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'address',
        'capacity',
        'description'
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
    public function locations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(ProductsMovement::class);
    }
    public function exceedsCapacity($amount)
    {
        return $this->capacity < $amount;
    }
}
