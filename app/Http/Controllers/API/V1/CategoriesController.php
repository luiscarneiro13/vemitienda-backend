<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Repositories\CategoriesRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use ApiResponser;


    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Mostrar Categorías para Usuario de la App",
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos los usuarios."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */

    public function index()
    {
        return $this->successResponse(['data' => CategoriesRepository::getCategories(-1)]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
