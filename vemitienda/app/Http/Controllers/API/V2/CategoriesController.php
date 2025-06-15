<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CategoryRequest;
use App\Repositories\CategoriesRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use ApiResponser;


    public function index()
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::getCategories(-1)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::storeCategory()]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    public function show($id)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::showCategory($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::updateCategory($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            return $this->successResponse(['data' => CategoriesRepository::deleteCategory($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse();
        }
    }
}
