<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CategoryRequest;
use App\Repositories\CategoriesRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use ApiResponser;

     /**
     * @OA\Get(
     *     tags={"Categories"},
     *     path="/v3/categories",
     *     security={{"bearer_token":{}}},
     *     summary="Mostrar Categorías para Usuario de la App",
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
            return $this->successResponse(['data' => CategoriesRepository::getCategories(-1)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    /**
     * @OA\Post(
     *     tags={"Categories"},
     *     path="/v3/categories",
     *     security={{"bearer_token":{}}},
     *     summary="Crear nueva categoría de un Usuario App",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name"},
     *               @OA\Property(property="name"),
     *            ),
     *        ),
     *    ),
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
    public function store(CategoryRequest $request)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::storeCategory()]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    /**
     * @OA\Get(
     *     tags={"Categories"},
     *     path="/v3/categories/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Ver categoría de un Usuario App por Id",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *      ),
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
    public function show($id)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::showCategory($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    /**
     * @OA\Put(
     *     tags={"Categories"},
     *     path="/v3/categories/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Actualizar categoría de un Usuario App",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *      ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="name",
     *           description="Nombre de la Categoría",
     *           type="string",
     *         ),
     *       ),
     *     ),
     *   ),
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
    public function update(CategoryRequest $request, $id)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::updateCategory($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Categories"},
     *     path="/v3/categories/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Borrar categoría de un Usuario App",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *      ),
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
    public function destroy($id)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::deleteCategory($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }
}
