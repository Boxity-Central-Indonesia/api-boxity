<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
