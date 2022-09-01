<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Repositories\CategoriesRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
class CategoriasController extends Controller
{
    use ApiResponser;

    public function index()
    {
        return $this->successResponse(['data' => CategoriesRepository::getCategories()]);
    }
}
