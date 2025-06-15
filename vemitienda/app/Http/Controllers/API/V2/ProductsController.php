<?php

namespace App\Http\Controllers\API\V2;

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

    public function index()
    {
        try {
            return $this->successResponse(['data' => ProductsRepository::getProductsUser(-1)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }


    public function store(ProductRequest $request)
    {
        $user = Auth::user();

        try {
            $datos = array_merge(['user_id' => $user->id], request()->all());
            $products = ProductsRepository::storeProduct($datos);
            return $this->successResponse(['data' => $products]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }


    public function show($id)
    {
        return $this->successResponse(['data' => ProductsRepository::getProductsUserId($id)]);
    }

    public function update(Request $request, $id)
    {
        try {
            return $this->successResponse(['data' =>  ProductsRepository::updateProduct($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

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
