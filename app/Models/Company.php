<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    protected $fillable = [
        'user_id',
        'name',
        'slogan',
        'email',
        'slug',
        'phone',
        'background_color_catalog',
        'is_shop',
        'theme_id',
        'onboarding'
    ];

    protected $appends = ['url_tienda', 'url_catalogo', 'url_tienda_new', 'slug'];

    public function getUrlTiendaAttribute()
    {
        $url = 'https://vemitienda.com.ve/catalogo/' . $this->attributes['slug'];
        return $this->attributes['url_tienda'] = $url;
    }

    public function getUrlTiendaNewAttribute()
    {
        $url = 'https://vemitienda.com.ve/' . $this->attributes['slug'];
        return $this->attributes['url_tienda_new'] = $url;
    }

    public function getUrlCatalogoAttribute()
    {
        $url = 'https://vemitienda.com.ve/catalogo/' . $this->attributes['slug'];
        return $this->attributes['url_catalogo'] = $url;
    }

    public function getSlugAttribute($value)
    {
        return $this->attributes['slug'] = Str::slug($this->attributes['name'], '-');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function logo()
    {
        return $this->morphOne(\App\Models\Image::class, 'imageable');
    }

    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
}
