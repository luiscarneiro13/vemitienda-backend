<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PlanUser extends Model
{
    use HasFactory;

    protected $table = 'plan_users';
    protected $fillable = ['plan_id', 'user_id', 'active'];

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }
}
