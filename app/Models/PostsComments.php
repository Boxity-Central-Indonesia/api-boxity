<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostsComments extends Model
{
    use HasFactory;
    protected $table = 'posts_comments';
    protected $fillable = ['posts_id', 'content'];

    public function post()
    {
        return $this->belongsTo(Posts::class, 'posts_id');
    }
}
