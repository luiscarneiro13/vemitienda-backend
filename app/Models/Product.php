<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Images;


class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['user_id', 'category_id', 'name', 'description', 'price', 'share'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function images()
    {
        return $this->morphMany(\App\Models\Image::class, 'imageable');
    }
}
