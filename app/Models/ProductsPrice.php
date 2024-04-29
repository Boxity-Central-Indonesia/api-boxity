<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class ProductsPrice extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'product_id',
        'selling_price',
        'buying_price',
        'discount_price',
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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
