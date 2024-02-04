<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    protected $table = 'packaging';
    protected $fillable = ['product_id', 'weight', 'package_type'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
