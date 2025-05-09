<?php

namespace App\Http\Controllers\WEB\V3;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    const PAGINATE = 20;

    public function index(Request $request, $slug)
    {
        $cat = $request->input('cat', 0);
        $query = $request->input('query', '');

        if (request()->cat && request()->cat > 0) {
            $cat = request()->cat;
        }
        $company = Company::with('logo', 'user')->where('slug', $slug)->first();
        if(!$company){
            // return redirect('https://vemitienda.com.ve');
        }
        $id_usuario = $company->user_id;
        $data = [
            "slug" => $slug,
            "company" => $company,
            "categories" => Category::where('user_id', $id_usuario)->has('products')->get(),
            "cat" => $cat,
            "query" => $query,
            "products" => $this->queryProductsIndex($id_usuario, $cat, $query)
        ];
        return view('V3/share.index', $data);
    }

    public function queryProductsIndex($id_usuario, $cat, $query)
    {
        return Product::query()
            ->with('image', 'category')
            ->where('share', 1)
            ->where('user_id', $id_usuario)
            ->when($cat && $cat > 0, function ($q) use ($cat) {
                $q->where('category_id', $cat);
            })
            ->when(is_string($query), function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%');
            })->paginate(self::PAGINATE)->appends(['cat' => $cat, 'query' => $query]);
    }
}
