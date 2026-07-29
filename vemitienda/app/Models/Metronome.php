<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metronome extends Model
{
    use HasFactory;

    protected $table = 'metronomes';
    protected $fillable = ['user_id', 'title', 'artist', 'bpm'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_metronome')->withTimestamps();
    }

    /**
     * Algunas canciones se agregan a una playlist solo para llevar el listado,
     * sin BPM asociado (no se les puede dar Play como metrónomo).
     */
    public function getHasMetronomeAttribute()
    {
        return !is_null($this->bpm);
    }
}
