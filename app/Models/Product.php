<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Images;


class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['name', 'price', 'description', 'category_id', 'user_id', 'compartir'];
    protected $appends = ['image1', 'image2', 'image1_base64', 'image2_base64'];

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

    public function getImage1Attribute()
    {
        $image = @$this->images[0] ? $this->images[0]->url : null;
        return $image;
    }

    public function getImage2Attribute()
    {
        $image = @$this->images[1] ? @$this->images[1]->url : null;
        return $image;
    }

    public function getImage1Base64Attribute()
    {
        $img = new Images();
        $image = @$this->images[0] ? $img->convertUrlToBase64($this->images[0]->url) : null;
        return $image;
    }

    public function getImage2Base64Attribute()
    {
        $img = new Images();
        $image = @$this->images[1] ? $img->convertUrlToBase64($this->images[1]->url) : null;
        return $image;
    }
}
