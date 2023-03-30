<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $fillable = ['name', 'email', 'company_id', 'phone', 'total'];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function details()
    {
        return $this->hasMany('App\Models\OrderDetail');
    }
}
