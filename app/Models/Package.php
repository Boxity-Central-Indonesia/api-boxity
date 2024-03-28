<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class Package extends Model
{
    use HasFactory;
    protected $fillable = [
        'package_name',
        'package_weight',
    ];
    public function packageProducts()
    {
        return $this->hasMany(PackageProduct::class, 'package_id');
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
    }
}
