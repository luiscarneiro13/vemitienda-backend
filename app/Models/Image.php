<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';
    protected $fillable = ['url', 'base64'];

    public function imageable()
    {
        return $this->morphTo();
    }
}
