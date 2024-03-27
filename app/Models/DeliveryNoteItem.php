<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class DeliveryNoteItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_note_id',
        'order_id',
        'product_id',
    ];

    // Relasi ke tabel Delivery Note, Order, dan Product
    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
