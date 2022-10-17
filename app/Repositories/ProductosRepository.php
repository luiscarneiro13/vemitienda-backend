<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductosRepository
{
    static function getProducts($limit = 10)
    {
        return Product::paginate($limit);
    }

    static function getProductsUser($limit = 10)
    {
        $user = Auth::user();

        $datos = Product::with('category', 'images')->where('user_id', $user->id);

        if ($limit == -1) {
            return $datos->get();
        } else {
            return $datos->paginate($limit);
        }
        return $datos;
    }

    static function getProductsUserId($id)
    {
        return Product::with('category', 'images')->where('id', $id)->first();
    }

    static function getProduct($id)
    {
        $product = Product::find($id);
        $product->delete();
        return $product;
    }
}
