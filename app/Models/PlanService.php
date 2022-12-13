<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanService extends Model
{
    protected $table = 'plan_services';
    protected $fillable = ['plan_id', 'service_id'];
}
