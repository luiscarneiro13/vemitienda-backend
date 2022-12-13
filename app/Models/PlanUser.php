<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanUser extends Model
{
    protected $table = 'plan_users';
    protected $fillable = ['plan_id', 'user_id', 'active'];
}
