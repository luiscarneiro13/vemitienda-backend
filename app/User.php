<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'country_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
        return $this->hasOne('App\Models\Company');
    }

    public function categories()
    {
        return $this->hasMany('App\Models\Category');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function plans()
    {
        return $this->belongsToMany('App\Models\Plan', 'plan_users')->withPivot('activo');
    }

    public function planUser()
    {
        return $this->hasOne('App\Models\PlanUser');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PlanUser');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }

    public function image()
    {
        return $this->morphMany(\App\Models\Image::class, 'imageable');
    }
}
