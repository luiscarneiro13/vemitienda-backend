<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Repositories\ProductosRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
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
        return $this->successResponse(['data' => ProductosRepository::getProductsUser(-1)]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        try {

            $data = [
                "user_id" => $user->id,
                "category_id" => request()->category_id,
                "name" => request()->name,
                "description" => request()->description || '',
                "price" => request()->price || '',
                "share" => request()->share || '',
            ];

            $product = Product::create($data);
            $product->save();

            if (request()->imagen1) {
                $urlImagen1 = $this->image->uploadImage('imagen1', 'products', 'do');
                $urlImages1Base64 = $this->image->convertUrlToBase64('imagen1');
                $product->images()->create(['base64' => $urlImages1Base64, 'url' => env('DO_URL_BASE') . '/' . $urlImagen1]);
            }

            if (request()->imagen2) {
                $urlImagen2 = $this->image->uploadImage('imagen2', 'products', 'do');
                $urlImages2Base64 = $this->image->convertUrlToBase64('imagen2');
                $product->images()->create(['base64' => $urlImages2Base64, 'url' => env('DO_URL_BASE') . '/' . $urlImagen2]);
            }
        } catch (\Throwable $th) {
            info($th);
            return $this->errorResponse(['message' => 'Ocurrió un error al tratar de crear el producto']);
        }

        return $this->successResponse(['message' => 'Producto creado con éxito', 'data' => $product]);
    }

    public function show($id)
    {
        return $this->successResponse(['data' => ProductosRepository::getProductsUserId($id)]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        try {

            $product = Product::with('images')->where('id', $id)->first();
            $product->user_id = $user->id;
            $product->category_id = request()->category_id;
            $product->name = request()->name;
            $product->description = request()->description || '';
            $product->price = request()->price || '';
            $product->share = request()->share || '';
            $product->save();

            if (request()->imagen1) {

                try {
                    if (@$product->images[0]) {
                        Storage::disk('do')->delete($product->images[0]->url);
                    }
                } catch (\Throwable $th) {
                }

                $urlImagen1 = $this->image->uploadImage('imagen1', 'products', 'do');
                $urlImages1Base64 = $this->image->convertUrlToBase64('imagen1');
                $product->images()->create(['base64' => $urlImages1Base64, 'url' => env('DO_URL_BASE') . '/' . $urlImagen1]);
            }

            if (request()->imagen2) {
                try {
                    if (@$product->images[1]) {
                        Storage::disk('do')->delete($product->images[1]->url);
                    }
                } catch (\Throwable $th) {
                }
                $urlImagen2 = $this->image->uploadImage('imagen2', 'products', 'do');
                $urlImages2Base64 = $this->image->convertUrlToBase64('imagen2');
                $product->images()->create(['base64' => $urlImages2Base64, 'url' => env('DO_URL_BASE') . '/' . $urlImagen2]);
            }
        } catch (\Throwable $th) {
            info($th);
            return $this->errorResponse(['message' => 'Ocurrió un error al tratar de crear el producto']);
        }

        return $this->successResponse(['message' => 'Producto creado con éxito', 'data' => $product]);
    }

    public function destroy($id)
    {
        return $this->successResponse(['data' => ProductosRepository::getProductsUserId($id)]);
    }
}
