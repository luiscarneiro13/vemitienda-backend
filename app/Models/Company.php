<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    protected $fillable = [
        'user_id',
        'name',
        'slogan',
        'email',
        'phone',
        'background_color_catalog'
    ];

    protected $appends = [
        'url_tienda'
    ];

    public function getUrlTiendaAttribute()
    {
        $id_usuario = Crypt::encrypt($this->attributes['user_id']);
        $url = 'https://vemitienda.online/share/' . $id_usuario;
        return $this->attributes['url_tienda'] = $url;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function logo()
    {
        return $this->morphOne(\App\Models\Image::class, 'imageable');
    }
}
