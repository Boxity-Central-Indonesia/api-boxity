<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name', 'code', 'description', 'price', 'category_id', 'warehouse_id',
        'type', 'animal_type', 'age', 'weight', 'health_status', 'stock',
        'unit_of_measure', 'raw_material',
    ];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->user_created = Auth::id();
        });
        self::updating(function ($model) {
            $model->user_updated = Auth::id();
        });
    }
    public function getDescriptionAttribute($description)
    {
        // Limit the description to 150 characters
        return \Str::limit($description, 30, '...');
    }
    public function category()
    {
        return $this->belongsTo(ProductsCategory::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductsPrice::class);
    }

    public function movements()
    {
        return $this->hasMany(ProductsMovement::class);
    }

    // Menambahkan relasi ke tabel packaging
    public function packaging()
    {
        return $this->hasMany(Packaging::class, 'product_id');
    }
    public function processingActivities()
    {
        return $this->hasMany(ProcessingActivity::class);
    }
}
