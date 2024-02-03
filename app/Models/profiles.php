<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap', 'photo_profile', 'full_address', 'phone_number', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Bisnis
    public function business()
    {
        return $this->hasOne(businesses::class);
    }
}
