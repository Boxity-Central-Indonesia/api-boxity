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
        'name',
        'code',
        'description',
        'price',
        'type',
        'subtype',
        'size',
        'color',
        'brand',
        'model',
        'sku',
        'stock',
        'image',
        'video',
        'raw_material',
        'unit_of_measure',
        'warehouse_id',
        'category_id',
        'weight',
        'animal_type',
        'age',
        'health_status'
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
    // Menambahkan relasi ke tabel slaughtering
    public function slaughtering()
    {
        return $this->hasOne(ManufacturerSlaughtering::class, 'product_id');
    }

    // Menambahkan relasi ke tabel carcasses melalui slaughtering
    public function carcasses()
    {
        return $this->hasManyThrough(ManufacturerCarcass::class, ManufacturerSlaughtering::class, 'product_id', 'slaughtering_id');
    }

    // Menambahkan relasi ke tabel packaging
    public function packaging()
    {
        return $this->hasMany(Packaging::class, 'product_id');
    }
    public function viscera()
    {
        return ManufacturerViscera::select('manufacturer_viscera.*')
            ->join('manufacturer_carcasses', 'manufacturer_carcasses.id', '=', 'manufacturer_viscera.carcass_id')
            ->join('manufacturer_slaughtering', 'manufacturer_slaughtering.id', '=', 'manufacturer_carcasses.slaughtering_id')
            ->where('manufacturer_slaughtering.product_id', $this->id);
    }
}
