<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }
}
