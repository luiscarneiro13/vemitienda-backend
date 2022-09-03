<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoriesRepository
{
    static function getCategories($limit = 10)
    {
        $user = Auth::user();
        $filtrar = request()->get('query');
        
        return Category::query()
            ->where('user_id', $user->id)
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);
    }

    static function storeCategory($web = false)
    {
        $insert = [
            'name' => request()->name
        ];

        return Category::updateOrCreate($insert);
    }

    static function editCategory($id)
    {
        return Category::find($id);
    }

    static function updateCategory($id)
    {
        $model = Category::find($id);
        $model->name        = request()->name;

        return $model->save();
    }

    static function deleteCategory($id)
    {
        $model = Category::find($id);
        return $model->delete();
    }
}
