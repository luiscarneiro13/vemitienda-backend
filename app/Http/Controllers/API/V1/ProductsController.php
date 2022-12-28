<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ProductRequest;
use App\Repositories\ProductsRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    use ApiResponser;

    public $image;

    public function __construct()
    {
        $this->image = new Images();
    }

    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products-user",
     *     security={{"bearer_token":{}}},
     *     summary="Mostrar los productos del Usuario de la App",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     )
     * )
     */
    public function index()
    {
        try {
            return $this->successResponse(['data' => ProductsRepository::getProductsUser(-1)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }


    /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/products-user",
     *     security={{"bearer_token":{}}},
     *     summary="Crear Producto de un Usuario App",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Datos del Producto",
     *        @OA\JsonContent(
     *           required={"category_id","name","description","price","share"},
     *           @OA\Property(property="category_id", type="string", format="category_id", example="4"),
     *           @OA\Property(property="name", type="string", format="name", example="Product X"),
     *           @OA\Property(property="description", type="string", format="description", example="Descripción del producto de prueba"),
     *           @OA\Property(property="price", type="string", format="price", example="100"),
     *           @OA\Property(property="share", type="string", format="share", example="1"),
     *        ),
     *     ),
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
    public function store(ProductRequest $request)
    {
        $user = Auth::user();

        try {
            $datos = array_merge(['user_id' => $user->id], request()->all());
            $company = ProductsRepository::storeProduct($datos);
            return $this->successResponse(['message' => 'Datos guardados', 'data' => $datos]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }


    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Ver producto de un Usuario App por Id",
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
        return $this->successResponse(['data' => ProductsRepository::getProductsUserId($id)]);
    }

    /**
     * @OA\Put(
     *     tags={"Products"},
     *     path="/products-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Actualizar producto de un Usuario App",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        description="Datos del producto",
     *        @OA\JsonContent(
     *           required={"category_id","name","description","price","share"},
     *           @OA\Property(property="category_id", type="string", format="category_id", example="4"),
     *           @OA\Property(property="name", type="string", format="name", example="Product X"),
     *           @OA\Property(property="description", type="string", format="description", example="Descripción del producto de prueba"),
     *           @OA\Property(property="price", type="string", format="price", example="100"),
     *           @OA\Property(property="share", type="string", format="share", example="1"),
     *        ),
     *     ),
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
    public function update(Request $request, $id)
    {
        try {
            return $this->successResponse([
                'message' => 'Datos guardados',
                'data' =>  ProductsRepository::updateProduct($id)
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Products"},
     *     path="/products/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Borrar producto de un Usuario App",
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
            // ACÁ ME FALTA ENVIAR A BORRAR TODAS LAS IMAGENES QUE TENÍA EL PRODUCTO
            return $this->successResponse(['data' => ProductsRepository::deleteProduct($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }
}
