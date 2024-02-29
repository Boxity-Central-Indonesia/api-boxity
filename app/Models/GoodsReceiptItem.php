<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'goods_receipt_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'quantity_due',
    ];

    // Relasi ke tabel Goods Receipt dan Product
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    protected $casts = [
        'created_at' => 'datetime:d M, Y',
        'updated_at' => 'datetime:d M, Y',
    ];
}
