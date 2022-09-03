<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductosRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductosController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        $this->image = new Images();
    }

    public function index()
    {
        return $this->successResponse(['data' => ProductosRepository::getProductsUser(-1)]);
    }

    public function storeProductUser(Request $request)
    {
        $user = Auth::user();
        try {

            $data = [
                "user_id" => $user->id,
                "category_id" => request()->category_id,
                "name" => request()->name,
                "description" => request()->description || '',
                "price" => request()->price || '',
                "compartir" => request()->compartir || '',
            ];

            $product = Product::create($data);
            $product->save();

            if (request()->imagen1) {
                $urlImagen1 = $this->image->uploadImage('imagen1', 'products', 'do');
                $product->images()->create(['url' => env('DO_URL_BASE') . '/' . $urlImagen1]);
            }

            if (request()->imagen2) {
                $urlImagen2 = $this->image->uploadImage('imagen2', 'products', 'do');
                $product->images()->create(['url' => env('DO_URL_BASE') . '/' . $urlImagen2]);
            }
        } catch (\Throwable $th) {
            info($th);
            return $this->errorResponse(['message' => 'Ocurrió un error al tratar de crear el producto']);
        }

        return $this->successResponse(['message' => 'Producto creado con éxito', 'data' => $product]);
    }
}
