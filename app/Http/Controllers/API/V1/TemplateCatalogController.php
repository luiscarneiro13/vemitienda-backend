<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\TemplateCatalog;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TemplateCatalogController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Get(
     *     tags={"Company"},
     *     path="/templates",
     *     security={{"bearer_token":{}}},
     *     summary="Mostrar plantillas para el catálogo de productos que exporta la App",
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
        try {
            return $this->successResponse(['data' => TemplateCatalog::all()]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }
}
