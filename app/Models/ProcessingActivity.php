<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessingActivity extends Model
{
    use HasFactory;
    protected $table = 'manufacturer_processing_activities';

    // Mass assignable attributes
    protected $fillable = [
        'carcass_id',
        'activity_type',
        'details',
    ];
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Relasi ke ManufacturerCarcass.
     */
    public function carcass()
    {
        return $this->belongsTo(ManufacturerCarcass::class, 'carcass_id');
    }
}
