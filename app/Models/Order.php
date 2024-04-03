<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Illuminate\Support\Facades\Auth;


class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'warehouse_id',
        'status',
        'details',
        'total_price',
        'taxes',
        'shipping_cost',
        'order_status',
        'order_type',
        'no_ref'
    ];
    protected $appends = ['kode_order'];
    public function getKodeOrderAttribute()
    {
        if ($this->created_at) {
            return 'ORD/' . $this->created_at->format('Y') . '/' . $this->created_at->format('m') . '/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
        }

        // Default value jika created_at null
        return 'ORD/unknown_date/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    // Hubungan ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
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
    public function processingActivities()
    {
        return $this->hasMany(ProcessingActivity::class);
    }
    public function vendorTransaction()
    {
        return $this->hasOne(VendorTransaction::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('quantity', 'price_per_unit', 'total_price')
            ->withTimestamps();
    }
    public function calculateTotalPrice()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->total_price;
        }) + ($this->taxes ?? 0) + ($this->shipping_cost ?? 0);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        static::saving(function ($order) {
            if ($order->products->isNotEmpty()) {
                $totalPrice = $order->products->reduce(function ($carry, $product) {
                    return $carry + ($product->pivot->quantity * $product->pivot->price_per_unit);
                }, 0);

                $order->total_price = $totalPrice + ($order->taxes ?? 0) + ($order->shipping_cost ?? 0);
            }
        });
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
