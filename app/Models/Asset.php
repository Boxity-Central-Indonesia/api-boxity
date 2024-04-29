<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;


class Asset extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name', 'code', 'type', 'description', 'acquisition_date', 'acquisition_cost', 'book_value', 'location_id', 'condition_id'
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        self::creating(function ($model) {
            $model->user_created = auth()->id();
        });
        self::updating(function ($model) {
            $model->user_updated = auth()->id();
        });
    }
    public function location()
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function condition()
    {
        return $this->belongsTo(AssetCondition::class, 'condition_id');
    }

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class, 'asset_id');
    }
}
