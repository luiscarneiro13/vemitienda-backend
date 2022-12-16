<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{

    /**
     * @OA\Info(title="API Ve mi Tienda", version="1.0", description="Se inicia sesión, Auth->login, se toma el token y se ingresa arriba en el botón Authorize")
     * @OA\Server(url="http://localhost:8000/api/v1")
     * @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     * )
     * @OA\Tag(
     *   name="Auth",
     *   description="Auth de la aplicación"
     * ),
     * @OA\Tag(
     *   name="Categories",
     *   description="Endpoints de Categorías"
     * )
     */

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
