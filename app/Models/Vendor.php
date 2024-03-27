<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Vendor extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'email',
        'date_of_birth',
        'transaction_type',
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
    public function contacts()
    {
        return $this->hasMany(VendorContact::class, 'vendors_id');
    }

    public function transactions()
    {
        return $this->hasMany(VendorTransaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getTransactionTypeLabelAttribute()
    {
        switch ($this->transaction_type) {
            case 'outbound':
                return 'Sales';
            case 'inbound':
                return 'Purchase';
            default:
                return 'Unknown';
        }
    }
}
