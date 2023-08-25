<?php

namespace App\Http\Controllers\WEB\V3;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function index(Request $request, $slug)
    {
        $cat = 0;
        if (request()->cat && request()->cat > 0) {
            $cat = request()->cat;
        }
        $company = Company::with('logo','user')->where('slug', $slug)->first();
        $id_usuario = $company->user_id;
        $total = Product::query()
            ->with('image', 'category')
            ->where('share', 1)
            ->where('user_id', $id_usuario)
            ->when($cat > 0, function ($q) {
                $q->where('category_id', request()->cat);
            })
            ->count();
        $data = [
            "slug" => $slug,
            "company" => $company,
            "categories" => Category::where('user_id', $id_usuario)->has('products')->get(),
            "pages" => (int)($total / 4),
            "cat" => $cat,
            "products" => Product::query()
                ->with('image', 'category')
                ->where('share', 1)
                ->where('user_id', $id_usuario)
                ->when($cat > 0, function ($q) {
                    $q->where('category_id', request()->cat);
                })
                // ->paginate(5)
                ->get()
        ];


        // if ($request->ajax()) {
        //     $view = view('V3/share.data', $data)->render();
        //     return response()->json(['html' => $view]);
        // }

        return view('V3/share.index', $data);
    }

    public function share(Request $request, $id_encriptado)
    {





        return view('share.index', $data);
    }

    public function shareAPI($id_encriptado)
    {
        // $data['cat'] = request()->cat;
        // $id_usuario = Crypt::decrypt($id_encriptado);
        // $data['company'] = Company::with('logo')->where('user_id', $id_usuario)->first();

        // $cat = null;
        // if (request()->cat && request()->cat > 0) {
        //     $cat = request()->cat;
        // }
        // $data['products'] = Product::query()
        //     ->with('image', 'category')
        //     ->where('share', 1)
        //     ->where('user_id', $id_usuario)
        //     ->when($cat, function ($q) {
        //         $q->where('category_id', request()->cat);
        //     })
        //     ->paginate(5);

        // return response()->json($data);
    }
}
