<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiContentGeneration extends Model
{
    use HasFactory;

    protected $table = 'ai_content_generations';
    protected $fillable = ['user_id', 'status', 'provider', 'brief', 'response', 'error_message'];
    protected $casts = [
        'brief' => 'array',
        'response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
