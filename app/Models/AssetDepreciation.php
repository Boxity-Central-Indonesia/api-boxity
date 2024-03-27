<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class AssetDepreciation extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'asset_depreciation';
    protected $fillable = [
        'asset_id', 'method', 'useful_life', 'residual_value', 'start_date', 'current_value'
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
        self::creating(function ($model) {
            $model->user_created = Auth::id();
        });
        self::updating(function ($model) {
            $model->user_updated = Auth::id();
        });
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
