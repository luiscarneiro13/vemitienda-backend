<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PlanService extends Model
{
    use HasFactory;

    protected $table = 'plan_services';
    protected $fillable = ['plan_id', 'service_id'];
}
