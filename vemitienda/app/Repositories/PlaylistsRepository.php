<?php

namespace App\Repositories;

use App\Models\Metronome;
use App\Models\Playlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'slug' => self::uniqueSlug(request()->name),
            'description' => request()->description,
        ]);
    }

    static function updatePlaylist(Playlist $playlist)
    {
        $playlist->name = request()->name;
        $playlist->slug = self::uniqueSlug(request()->name, $playlist->id);
        $playlist->description = request()->description;
        $playlist->save();

        return $playlist;
    }

    /**
     * Genera un slug único (a nivel global, usado en la URL pública /playlist/{slug})
     * a partir del nombre, agregando un sufijo numérico si ya existe.
     */
    static function uniqueSlug(string $name, $excludeId = null)
    {
        $base = Str::slug($name, '-') ?: 'playlist';
        $slug = $base;
        $i = 2;

        while (
            Playlist::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /**
     * Playlist pública (sin scope de usuario) buscada por su slug, usada en /playlist/{slug}.
     */
    static function getPublicPlaylistBySlug($slug)
    {
        return Playlist::with('metronomes')->where('slug', $slug)->firstOrFail();
    }

    static function deletePlaylist(Playlist $playlist)
    {
        return $playlist->delete();
    }

    static function attachMetronome(Playlist $playlist, Metronome $metronome)
    {
        $nextPosition = ($playlist->metronomes()->max('playlist_metronome.position') ?? 0) + 1;

        $playlist->metronomes()->syncWithoutDetaching([
            $metronome->id => ['position' => $nextPosition],
        ]);

        return $playlist;
    }

    static function detachMetronome(Playlist $playlist, Metronome $metronome)
    {
        $playlist->metronomes()->detach($metronome->id);

        return $playlist;
    }

    /**
     * Persiste el nuevo orden de canciones de la playlist.
     * $orderedIds = ids de metrónomos en el orden deseado (de arriba hacia abajo).
     */
    static function reorderMetronomes(Playlist $playlist, array $orderedIds)
    {
        $ownIds = $playlist->metronomes()->pluck('metronomes.id')->map(fn ($id) => (int) $id)->all();

        $position = 1;
        foreach ($orderedIds as $metronomeId) {
            if (!in_array((int) $metronomeId, $ownIds, true)) {
                continue;
            }

            $playlist->metronomes()->updateExistingPivot($metronomeId, ['position' => $position]);
            $position++;
        }

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
