<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    use HasFactory;

    protected $table = 'post_categories';
    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'category_id');
    }

}
