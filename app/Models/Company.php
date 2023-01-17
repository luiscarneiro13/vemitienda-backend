<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    protected $fillable = ['user_id', 'name', 'slogan', 'email', 'phone', 'template_catalog_id', 'background_color_catalog'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function logo()
    {
        return $this->morphOne(\App\Models\Image::class, 'imageable');
    }

    public function template()
    {
        return $this->belongsTo(\App\Models\TemplateCatalog::class);
    }
}
