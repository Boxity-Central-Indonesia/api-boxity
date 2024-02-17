<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class businesses extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_bisnis', 'full_address', 'email', 'website', 'phone_number', 'pic_business', 'business_logo', 'bank_account_name', 'bank_branch', 'bank_account_number', 'profile_id',
    ];

    // Relasi ke Profile
    public function profile()
    {
        return $this->belongsTo(profiles::class, 'profile_id');
    }
}
