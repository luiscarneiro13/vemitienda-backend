<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';
    protected $fillable = ['name', 'price', 'quantity'];

    public function plan_users()
    {
        return $this->hasMany('App\Models\PlanUser');
    }
}
