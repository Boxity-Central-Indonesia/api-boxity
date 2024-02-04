<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturerCarcass extends Model
{
    protected $table = 'manufacturer_carcasses';
    protected $fillable = ['slaughtering_id', 'weight_after_slaughter', 'quality_grade'];

    public function slaughtering()
    {
        return $this->belongsTo(ManufacturerSlaughtering::class, 'slaughtering_id');
    }
    public function processingActivities()
    {
        return $this->hasMany(ProcessingActivity::class, 'carcass_id');
    }
}
