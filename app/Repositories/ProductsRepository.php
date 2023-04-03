<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductsRepository
{
    static function getProducts($limit = 10)
    {
        return Product::paginate($limit);
    }

    static function getProductsUser($limit = 10)
    {
        $user = Auth::user();

        $datos = Product::with('category', 'image')->where('user_id', $user->id);

        if ($limit == -1) {
            return $datos->get();
        } else {
            return $datos->paginate($limit);
        }
        return $datos;
    }

    static function getProductsUserId($id)
    {
        return Product::with('category', 'image')->where('id', $id)->first();
    }

    static function getProduct($id)
    {
        $product = Product::find($id);
        $product->delete();
        return $product;
    }

    static function storeProduct($insert)
    {
        $product = Product::create($insert);
        return Product::with('category', 'image')->where('id', $product->id)->first();
    }

    static function deleteProduct($id)
    {
        $model = Product::find($id);
        return $model->delete();
    }

    static function updateProduct($id)
    {
        $user = Auth::user();
        $model = Product::find($id);
        $model->user_id = $user->id;
        $model->name = request()->name;
        $model->available    = request()->available;
        $model->category_id = request()->category_id;
        $model->description = request()->description;
        $model->price = request()->price ? request()->price : 0;
        $model->share = request()->share ? request()->share : 0;
        $model->save();
        return $model->with('category', 'image')->first();
    }
}
