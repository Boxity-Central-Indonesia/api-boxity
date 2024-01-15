<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;


class Asset extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'acquisition_date',
        'acquisition_cost',
        'book_value',
        'location_id',
        'condition_id',
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
    public function location()
    {
        return $this->belongsTo(AssetLocation::class);
    }

    public function condition()
    {
        return $this->belongsTo(AssetCondition::class);
    }

    public function depreciation()
    {
        return $this->hasOne(AssetDepreciation::class);
    }
}
