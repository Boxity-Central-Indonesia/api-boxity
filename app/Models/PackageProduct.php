<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageProduct extends Model
{
    use HasFactory;
    protected $table = 'package_product';
    protected $fillable = [
        'package_id',
        'product_id',
    ];
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
