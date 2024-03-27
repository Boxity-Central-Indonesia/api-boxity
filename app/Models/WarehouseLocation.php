<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
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
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
