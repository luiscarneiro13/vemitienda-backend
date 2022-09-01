<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\ProductosRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    use ApiResponser;

    public function index()
    {
        return $this->successResponse(['data' => ProductosRepository::getProductsUser()]);
    }
}
