<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerified extends Model
{
    use HasFactory;

    protected $table = 'user_verifieds';
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
