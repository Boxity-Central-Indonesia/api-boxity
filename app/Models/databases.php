<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class databases extends Model
{
    use HasFactory;
    protected $table = 'databases';

    protected $fillable = [
        'name',
    ];
}
