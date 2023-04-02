<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Repositories\ThemesRepository;
use Illuminate\Http\Request;

class ThemesController extends Controller
{
    /**
     * @OA\Get(
     *     tags={"Company"},
     *     path="/company-theme",
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
        return $this->successResponse(['data' => ThemesRepository::getThemes()]);
    }
}
