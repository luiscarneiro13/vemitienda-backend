<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $fillable = ['user_id', 'name', 'slogan', 'email', 'phone'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function logo()
    {
        return $this->morphOne(\App\Models\Image::class, 'imageable');
    }
}
