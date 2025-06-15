<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Repositories\ThemesRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ThemesController extends Controller
{
    use ApiResponser;
    /**
     * @OA\Get(
     *     tags={"Themes"},
     *     path="/v3/themes",
     *     security={{"bearer_token":{}}},
     *     summary="Mostrar los temas disponibles",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function index()
    {
        return $this->successResponse(['data' => ThemesRepository::getThemes(-1)]);
    }
}
