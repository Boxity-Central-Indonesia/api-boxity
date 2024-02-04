<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class VendorTransaction extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'vendors_id', 'amount', 'product_id', 'unit_price', 'total_price', 'taxes', 'shipping_cost', 'order_id'
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
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendors_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
