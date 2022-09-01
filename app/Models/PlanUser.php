<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanUser extends Model
{
    protected $table = 'plan_users';
    protected $fillable = ['name'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }
}
