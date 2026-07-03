<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTemplate extends Model
{
    use HasFactory;

    protected $table = 'post_templates';
    protected $fillable = ['user_id', 'title', 'description', 'prompt', 'tone', 'objective', 'audience', 'color'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
