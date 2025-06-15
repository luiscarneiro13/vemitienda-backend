<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Images;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = ['code', 'user_id', 'category_id', 'name', 'description', 'price', 'share', 'available'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function image()
    {
        return $this->morphMany(\App\Models\Image::class, 'imageable');
    }

    public function orderDetails()
    {
        return $this->hasMany('App\Models\OrderDetail');
    }
}
