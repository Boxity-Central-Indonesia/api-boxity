<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class PostsCategories extends Model
{
    use HasFactory;
    protected $table = 'posts_categories';
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->hasMany(Posts::class);
    }
}
