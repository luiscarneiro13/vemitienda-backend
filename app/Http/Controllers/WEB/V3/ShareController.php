<?php

namespace App\Http\Controllers\WEB\V3;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function index($slug)
    {
        $cat = 0;
        if (request()->cat && request()->cat > 0) {
            $cat = request()->cat;
        }
        $company = Company::with('logo', 'user')->where('slug', $slug)->first();
        $id_usuario = $company->user_id;
        $data = [
            "slug" => $slug,
            "company" => $company,
            "categories" => Category::where('user_id', $id_usuario)->has('products')->get(),
            "cat" => $cat,
            "products" => Product::query()
                ->has('image')->has('category')
                ->with('image', 'category')
                ->where('share', 1)
                ->where('user_id', $id_usuario)
                ->when($cat > 0, function ($q) {
                    $q->where('category_id', request()->cat);
                })
                ->when(request()->query, function ($q) {
                    $q->where('name', 'LIKE', '%' . request()->input('query') . '%');
                })
                ->get()
        ];

        return view('V3/share.index', $data);
    }
}
