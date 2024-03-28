<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class ProcessingActivity extends Model
{
    use HasFactory;

    protected $table = 'manufacturer_processing_activities';

    protected $fillable = [
        'order_id',
        'product_id',
        'activity_type',
        'activity_date', // Tambahkan jika belum ada
        'status_activities', // Tambahkan jika belum ada
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
    }
}
