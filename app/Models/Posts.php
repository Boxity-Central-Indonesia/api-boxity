<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CreatedAtDescScope;

class Posts extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = ['user_id', 'title', 'body', 'category_id', 'cover_image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(PostsCategories::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(PostsComments::class);
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CreatedAtDescScope());
    }
}
