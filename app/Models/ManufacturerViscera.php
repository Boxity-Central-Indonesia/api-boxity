<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturerViscera extends Model
{
    protected $table = 'manufacturer_viscera';
    protected $fillable = ['carcass_id', 'type', 'handling_method'];

    public function carcass()
    {
        return $this->belongsTo(ManufacturerCarcass::class, 'carcass_id');
    }
}
