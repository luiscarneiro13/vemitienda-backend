<?php

namespace App\Http\Controllers\WEB\ADMIN;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\CategoryRequest;
use App\Repositories\CategoriesRepository;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $datos['infoData'] = CategoriesRepository::getCategories(9);

        $datos['nombreColumnas'] = collect([
            'Nombre' => 'name',
        ]);

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('admin.categories.index', $data);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        try {
            CategoriesRepository::storeCategory(true);
            $response = [
                'message' => 'Categoría creada',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('categories.index')->with($response);
    }

    public function edit($id)
    {
        $data['category'] = CategoriesRepository::editCategory($id);
        return view('admin.categories.edit', $data);
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            CategoriesRepository::updateCategory($id);
            $response = [
                'message' => 'Categoría actualizada',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('categories.index')->with($response);
    }

    public function destroy($id)
    {
        try {
            CategoriesRepository::deleteCategory($id);
            $response = [
                'message' => 'Categoría eliminada',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('categories.index')->with($response);
    }
}
