<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturerSlaughtering extends Model
{
    protected $table = 'manufacturer_slaughtering';
    protected $fillable = ['product_id', 'slaughter_date', 'method'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
