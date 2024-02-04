<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'warehouse_id',
        'product_id',
        'status',
        'details',
        'price_per_unit',
        'total_price',
        'quantity',
        'taxes',
        'shipping_cost'
    ];

    // Hubungan ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Hubungan ke Invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function vendorTransaction()
    {
        return $this->hasOne(VendorTransaction::class);
    }
}
