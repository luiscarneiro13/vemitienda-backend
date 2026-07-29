<?php

namespace App\Repositories;

use App\Models\Metronome;
use App\Models\Playlist;
use Illuminate\Support\Facades\Auth;

class PlaylistsRepository
{
    static function getPlaylistsUser($limit = 9)
    {
        $datos = Playlist::withCount('metronomes')->where('user_id', Auth::id())->orderByDesc('id');

        if ($limit == -1) {
            return $datos->get();
        }

        return $datos->paginate($limit);
    }

    static function storePlaylist()
    {
        return Playlist::create([
            'user_id' => Auth::id(),
            'name' => request()->name,
            'description' => request()->description,
        ]);
    }

    static function updatePlaylist(Playlist $playlist)
    {
        $playlist->name = request()->name;
        $playlist->description = request()->description;
        $playlist->save();

        return $playlist;
    }

    static function deletePlaylist(Playlist $playlist)
    {
        return $playlist->delete();
    }

    static function attachMetronome(Playlist $playlist, Metronome $metronome)
    {
        $playlist->metronomes()->syncWithoutDetaching([$metronome->id]);

        return $playlist;
    }

    static function detachMetronome(Playlist $playlist, Metronome $metronome)
    {
        $playlist->metronomes()->detach($metronome->id);

        return $playlist;
    }

    static function getAvailableMetronomes(Playlist $playlist)
    {
        $inPlaylist = $playlist->metronomes()->pluck('metronomes.id');

        return Metronome::where('user_id', Auth::id())
            ->whereNotIn('id', $inPlaylist)
            ->orderBy('title')
            ->get();
    }
}
