<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name', 'code', 'description', 'price', 'category_id', 'warehouse_id',
        'type', 'animal_type', 'age', 'weight', 'health_status', 'stock',
        'unit_of_measure', 'raw_material','image_product',
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        self::creating(function ($model) {
            $model->user_created = auth()->id();
            // Generate a unique product code
            $uniqueIdentifier = uniqid();
            $model->code = 'PRD' . strtoupper(substr(md5($uniqueIdentifier), 0, 6));
        });
        self::updating(function ($model) {
            $model->user_updated = auth()->id();
        });
    }
    public function getImageProductAttribute($image_product)
    {
        return $image_product ? $image_product : 'https://res.cloudinary.com/boxity-id/image/upload/v1709745192/39b09e1f-0446-4f78-bbf1-6d52d4e7e4df.png';
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
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products');
    }
}
