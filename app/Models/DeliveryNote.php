<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

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
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
    }
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
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
