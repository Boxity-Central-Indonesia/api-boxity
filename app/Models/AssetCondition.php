<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class AssetCondition extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'condition',
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
    public function assets()
    {
        return $this->hasMany(Asset::class, 'condition_id');
    }
}
