<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $fillable = ['plan_user_id', 'start_date', 'end_date', 'paid_out'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function paymentDetails()
    {
        return $this->hasMany('App\Models\PaymentDetails');
    }
}
