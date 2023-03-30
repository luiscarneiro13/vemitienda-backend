<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $fillable = ['pedido_id', 'product_id', 'name', 'price', 'quantity', 'image'];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

}
