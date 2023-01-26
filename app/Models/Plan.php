<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';
    protected $fillable = ['name', 'quantity'];

    public function planUser()
    {
        return $this->belongsToMany('App\Models\PlanUser', 'plan_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'plan_users');
    }
}
