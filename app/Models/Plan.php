<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';
    protected $fillable = ['name'];

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'plan_services');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'plan_users');
    }
}
