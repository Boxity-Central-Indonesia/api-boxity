<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class WarehouseLocation extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'warehouse_id',
        'number',
        'capacity',
        'length',
        'width',
        'height'
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
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
