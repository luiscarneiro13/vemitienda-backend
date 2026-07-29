<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $table = 'playlists';
    protected $fillable = ['user_id', 'name', 'description'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function metronomes()
    {
        return $this->belongsToMany(Metronome::class, 'playlist_metronome')
            ->withPivot('position')
            ->withTimestamps()
            ->orderBy('playlist_metronome.position');
    }
}
