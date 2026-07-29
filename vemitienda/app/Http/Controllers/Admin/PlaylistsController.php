<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\PlaylistRequest;
use App\Models\Metronome;
use App\Models\Playlist;
use App\Repositories\PlaylistsRepository;
use Illuminate\Http\Request;

class PlaylistsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos['infoData'] = PlaylistsRepository::getPlaylistsUser(9);
        $data['data'] = $datos;

        return view('Admin.Playlists.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.Playlists.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\WEB\PlaylistRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlaylistRequest $request)
    {
        $playlist = PlaylistsRepository::storePlaylist();

        return redirect()->route('playlists.show', $playlist->id);
    }

    /**
     * Display the specified resource with its metronomes.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $playlist = Playlist::with('metronomes')->findOrFail($id);
        $this->authorize('view', $playlist);

        $data['playlist'] = $playlist;
        $data['availableMetronomes'] = PlaylistsRepository::getAvailableMetronomes($playlist);

        return view('Admin.Playlists.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('update', $playlist);

        $data['playlist'] = $playlist;

        return view('Admin.Playlists.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\WEB\PlaylistRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PlaylistRequest $request, $id)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('update', $playlist);

        PlaylistsRepository::updatePlaylist($playlist);

        return redirect()->route('playlists.show', $playlist->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('delete', $playlist);

        PlaylistsRepository::deletePlaylist($playlist);

        return redirect()->route('playlists.index');
    }

    /**
     * Agrega un metrónomo propio del usuario a la playlist.
     *
     * @param  int  $playlistId
     * @param  int  $metronomeId
     * @return \Illuminate\Http\Response
     */
    public function attach($playlistId, $metronomeId)
    {
        $playlist = Playlist::findOrFail($playlistId);
        $this->authorize('update', $playlist);

        $metronome = Metronome::findOrFail($metronomeId);
        $this->authorize('view', $metronome);

        PlaylistsRepository::attachMetronome($playlist, $metronome);

        return redirect()->route('playlists.show', $playlist->id);
    }

    /**
     * Quita un metrónomo de la playlist.
     *
     * @param  int  $playlistId
     * @param  int  $metronomeId
     * @return \Illuminate\Http\Response
     */
    public function detach($playlistId, $metronomeId)
    {
        $playlist = Playlist::findOrFail($playlistId);
        $this->authorize('update', $playlist);

        $metronome = Metronome::findOrFail($metronomeId);
        $this->authorize('view', $metronome);

        PlaylistsRepository::detachMetronome($playlist, $metronome);

        return redirect()->route('playlists.show', $playlist->id);
    }

    /**
     * Persiste el nuevo orden (drag & drop) de las canciones de la playlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request, $id)
    {
        $playlist = Playlist::findOrFail($id);
        $this->authorize('update', $playlist);

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        PlaylistsRepository::reorderMetronomes($playlist, $request->order);

        return response()->json(['success' => true]);
    }
}
