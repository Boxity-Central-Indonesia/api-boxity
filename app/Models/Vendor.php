<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    public function contacts()
    {
        return $this->hasMany(VendorContact::class, 'vendors_id');
    }

    public function transactions()
    {
        return $this->hasMany(VendorTransaction::class);
    }

    public function getTransactionTypeLabelAttribute()
    {
        switch ($this->transaction_type) {
            case 'outbound':
                return 'Outbound';
            case 'inbound':
                return 'Inbound';
            default:
                return 'Unknown';
        }
    }
}
