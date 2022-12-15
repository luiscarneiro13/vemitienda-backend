<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['plan_user_id', 'start_date', 'end_date', 'paid_out'];

    public function planUser()
    {
        return $this->belongsTo('App\Models\PlanUser');
    }

    public function paymentDetails()
    {
        return $this->hasMany('App\Models\PaymentDetails');
    }
}
