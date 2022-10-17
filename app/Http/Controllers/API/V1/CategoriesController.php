<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Repositories\CategoriesRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use ApiResponser;

    public function index()
    {
        return $this->successResponse(['data' => CategoriesRepository::getCategories(-1)]);
    }

    public function store(Request $request)
    {
        //
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
