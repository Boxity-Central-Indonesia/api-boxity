<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class ProductsMovement extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;
    protected $table = 'products_movements';
    protected $fillable = ['product_id', 'warehouse_id', 'movement_type', 'quantity', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
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

    public function getMovementTypeLabelAttribute()
    {
        switch ($this->movement_type) {
            case 'purchase':
                return 'Pembelian';
            case 'sale':
                return 'Penjualan';
            case 'transfer':
                return 'Transfer';
            default:
                return 'Unknown';
        }
    }
}
