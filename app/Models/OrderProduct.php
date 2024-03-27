<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'price_per_unit',
        'total_price'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
