<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'alamat',
        'website',
        'no_handphone',
        'user_id',
    ];
}
