<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['plan_user_id', 'start_date', 'end_date', 'paid_out'];
}
