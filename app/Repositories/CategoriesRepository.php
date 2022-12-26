<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoriesRepository
{

    private $user;

    static function getCategories($limit = 10)
    {
        $filtrar = request()->get('query');
        $user = Auth::user();

        $datos = Category::query()
            ->where('user_id', $user->id)
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                return $q;
            });

        if ($limit == -1) {
            return $datos->get();
        } else {
            return $datos->paginate($limit);
        }
    }

    static function showCategory($id)
    {
        $user = Auth::user();

        return Category::query()
            ->where('user_id', $user->id)
            ->where('id', $id)->first();
    }

    static function storeCategory($web = false)
    {
        $user = Auth::user();

        $insert = [
            'name' => request()->name,
            'user_id' => $user->id
        ];

        return Category::updateOrCreate($insert);
    }

    static function editCategory($id)
    {
        return Category::find($id);
    }

    static function updateCategory($id)
    {
        $user = Auth::user();

        $model = Category::find($id);
        $model->name = request()->name;
        $model->user_id = $user->id;
        $model->save();
        return $model;
    }

    static function deleteCategory($id)
    {
        $model = Category::find($id);
        return $model->delete();
    }
}
