<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $fillable = ['name', 'email', 'company_id', 'phone', 'total', 'status_id'];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function details()
    {
        return $this->hasMany('App\Models\OrderDetail');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus');
    }
}
