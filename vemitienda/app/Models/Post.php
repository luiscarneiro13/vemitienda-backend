<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $fillable = ['name', 'slug', 'extract', 'body', 'status', 'user_id', 'category_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\PostCategory', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag');
    }

    public function image()
    {
        return $this->morphOne(\App\Models\Image::class, 'imageable');
    }
}
