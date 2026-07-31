<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\PlaylistRequest;
use App\Models\Metronome;
use App\Models\Playlist;
use App\Repositories\PlaylistsRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PlaylistsController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user",
     *     security={{"bearer_token":{}}},
     *     summary="Listar las playlists del usuario autenticado",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function index()
    {
        try {
            return $this->successResponse(['data' => PlaylistsRepository::getPlaylistsUser(-1)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user",
     *     security={{"bearer_token":{}}},
     *     summary="Crear una playlist del usuario autenticado",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Datos de la playlist",
     *        @OA\JsonContent(
     *           required={"name"},
     *           @OA\Property(property="name", type="string", example="Ensayo banda"),
     *           @OA\Property(property="description", type="string", example="Canciones para el ensayo del sábado"),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function store(PlaylistRequest $request)
    {
        try {
            return $this->successResponse(['data' => PlaylistsRepository::storePlaylist()]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Ver una playlist propia con sus canciones (ordenadas)",
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $playlist = Playlist::with('metronomes')->findOrFail($id);
            $this->authorize('view', $playlist);

            return $this->successResponse(['data' => $playlist]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Actualizar nombre/descripción de una playlist propia",
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function update(PlaylistRequest $request, $id)
    {
        try {
            $playlist = Playlist::findOrFail($id);
            $this->authorize('update', $playlist);

            return $this->successResponse(['data' => PlaylistsRepository::updatePlaylist($playlist)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Eliminar una playlist propia",
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $playlist = Playlist::findOrFail($id);
            $this->authorize('delete', $playlist);
            PlaylistsRepository::deletePlaylist($playlist);

            return $this->successResponse(['data' => null]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user/{playlist}/metronomes/{metronome}",
     *     security={{"bearer_token":{}}},
     *     summary="Agregar un metrónomo propio a una playlist propia",
     *     @OA\Parameter(name="playlist", in="path", required=true),
     *     @OA\Parameter(name="metronome", in="path", required=true),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function attach($playlistId, $metronomeId)
    {
        try {
            $playlist = Playlist::findOrFail($playlistId);
            $this->authorize('update', $playlist);

            $metronome = Metronome::findOrFail($metronomeId);
            $this->authorize('view', $metronome);

            PlaylistsRepository::attachMetronome($playlist, $metronome);

            return $this->successResponse(['data' => $playlist->load('metronomes')]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user/{playlist}/metronomes/{metronome}",
     *     security={{"bearer_token":{}}},
     *     summary="Quitar un metrónomo de una playlist propia",
     *     @OA\Parameter(name="playlist", in="path", required=true),
     *     @OA\Parameter(name="metronome", in="path", required=true),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function detach($playlistId, $metronomeId)
    {
        try {
            $playlist = Playlist::findOrFail($playlistId);
            $this->authorize('update', $playlist);

            $metronome = Metronome::findOrFail($metronomeId);
            $this->authorize('view', $metronome);

            PlaylistsRepository::detachMetronome($playlist, $metronome);

            return $this->successResponse(['data' => $playlist->load('metronomes')]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Playlists"},
     *     path="/v3/playlists-user/{id}/reorder",
     *     security={{"bearer_token":{}}},
     *     summary="Persistir el nuevo orden (drag & drop) de las canciones de una playlist propia",
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\RequestBody(
     *        required=true,
     *        description="Array de ids de metrónomo en el orden deseado",
     *        @OA\JsonContent(
     *           required={"order"},
     *           @OA\Property(property="order", type="array", @OA\Items(type="integer"), example={3,1,2}),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function reorder(Request $request, $id)
    {
        try {
            $playlist = Playlist::findOrFail($id);
            $this->authorize('update', $playlist);

            $request->validate([
                'order' => 'required|array',
                'order.*' => 'integer',
            ]);

            PlaylistsRepository::reorderMetronomes($playlist, $request->order);

            return $this->successResponse(['data' => $playlist->load('metronomes')]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }
}
