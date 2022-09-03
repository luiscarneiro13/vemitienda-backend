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
        return Product::with('category','image')->where('user_id', $user->id)->paginate($limit);
    }
}

