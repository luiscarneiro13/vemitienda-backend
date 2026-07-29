<?php

namespace App\Repositories;

use App\Models\Metronome;
use Illuminate\Support\Facades\Auth;

class MetronomesRepository
{
    static function getMetronomesUser($limit = 9)
    {
        $datos = Metronome::where('user_id', Auth::id())->orderByDesc('id');

        if ($limit == -1) {
            return $datos->get();
        }

        return $datos->paginate($limit);
    }

    static function storeMetronome()
    {
        return Metronome::create([
            'user_id' => Auth::id(),
            'title' => request()->title,
            'artist' => request()->artist,
            'bpm' => request()->bpm,
        ]);
    }

    static function updateMetronome(Metronome $metronome)
    {
        $metronome->title = request()->title;
        $metronome->artist = request()->artist;
        $metronome->bpm = request()->bpm;
        $metronome->save();

        return $metronome;
    }

    static function deleteMetronome(Metronome $metronome)
    {
        return $metronome->delete();
    }
}
