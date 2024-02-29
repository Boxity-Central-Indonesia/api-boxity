<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'date',
        'warehouse_id',
        'vendor_id',
        'details',
    ];

    // Relasi ke tabel Warehouse dan Vendor
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Relasi ke tabel Delivery Note Items
    public function deliveryNoteItems()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }
    protected $casts = [
        'created_at' => 'datetime:d M, Y',
        'updated_at' => 'datetime:d M, Y',
    ];
}
