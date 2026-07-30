<?php

namespace App\Http\Controllers;

use App\Repositories\PlaylistsRepository;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Vista pública de una playlist (solo lectura + reproducir + reordenar).
     * No requiere autenticación ni pertenencia: se accede por su slug único.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $playlist = PlaylistsRepository::getPublicPlaylistBySlug($slug);

        return view('Playlists.public', ['playlist' => $playlist]);
    }

    /**
     * Persiste el nuevo orden (drag & drop) de las canciones de la playlist pública.
     * Es la única escritura permitida desde la vista pública.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request, $slug)
    {
        $playlist = PlaylistsRepository::getPublicPlaylistBySlug($slug);

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        PlaylistsRepository::reorderMetronomes($playlist, $request->order);

        return response()->json(['success' => true]);
    }
}
