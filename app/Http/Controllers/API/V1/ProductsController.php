<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use ApiResponser;

    public $image;
    public $user;

    public function __construct()
    {
        $this->image = new Images();
        $this->user = Auth->user();
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {

        try {

            $data = [
                "user_id" => $this->user->id,
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
