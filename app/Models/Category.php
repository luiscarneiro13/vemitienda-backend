<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['user_id', 'name'];

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
