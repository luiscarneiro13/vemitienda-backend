<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetails extends Model
{
    protected $table = 'payment_details';
    protected $fillable = ['paymen_id', 'payment_method_id', 'reference_number', 'image', 'verified'];

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod');
    }
}
