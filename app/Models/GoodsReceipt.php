<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Illuminate\Support\Facades\Auth;

class GoodsReceipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'warehouse_id',
        'status',
        'details',
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
    protected $appends = ['kodeGoodsReceipt'];
// Atribut Kode Goods Receipt
public function getKodeGoodsReceiptAttribute()
{
    if (!is_null($this->created_at)) {
        return 'GR/' . $this->created_at->format('Y') . '/' . $this->created_at->format('m') . '/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
    // Relasi ke tabel Order dan Warehouse
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Relasi ke tabel Goods Receipt Items
    public function goodsReceiptItems()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
