<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\MetronomeRequest;
use App\Models\Metronome;
use App\Repositories\MetronomesRepository;
use App\Traits\ApiResponser;

class MetronomesController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     tags={"Metronomes"},
     *     path="/v3/metronomes-user",
     *     security={{"bearer_token":{}}},
     *     summary="Listar los metrónomos (canciones) del usuario autenticado",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function index()
    {
        try {
            return $this->successResponse(['data' => MetronomesRepository::getMetronomesUser(-1)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Metronomes"},
     *     path="/v3/metronomes-user",
     *     security={{"bearer_token":{}}},
     *     summary="Crear un metrónomo (canción) del usuario autenticado",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Datos del metrónomo",
     *        @OA\JsonContent(
     *           required={"title"},
     *           @OA\Property(property="title", type="string", example="Bohemian Rhapsody"),
     *           @OA\Property(property="artist", type="string", example="Queen"),
     *           @OA\Property(property="bpm", type="integer", example=120, description="Opcional: dejar vacío si la canción no lleva metrónomo"),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function store(MetronomeRequest $request)
    {
        try {
            return $this->successResponse(['data' => MetronomesRepository::storeMetronome()]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Metronomes"},
     *     path="/v3/metronomes-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Actualizar un metrónomo propio del usuario autenticado",
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function update(MetronomeRequest $request, $id)
    {
        try {
            $metronome = Metronome::findOrFail($id);
            $this->authorize('update', $metronome);

            return $this->successResponse(['data' => MetronomesRepository::updateMetronome($metronome)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Metronomes"},
     *     path="/v3/metronomes-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Eliminar un metrónomo propio del usuario autenticado",
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
            $metronome = Metronome::findOrFail($id);
            $this->authorize('delete', $metronome);
            MetronomesRepository::deleteMetronome($metronome);

            return $this->successResponse(['data' => null]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th->getMessage()]);
        }
    }
}
