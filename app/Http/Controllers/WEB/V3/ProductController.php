<?php

namespace App\Http\Controllers\WEB\V3;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\PlanUser;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function productList(Request $request, $slug)
    {
        $data['company'] = Company::with('theme')->where('slug', $slug)->first();

        if (!$data['company']) {
            return redirect()->route('home');
        }

        // $data['products'] = Product::with('image', 'category')->where('user_id', $data['company']->user_id)->get();

        $data['cat'] = request()->cat;
        $id_usuario = $data['company']->user_id;
        $planUser = PlanUser::where('user_id', $id_usuario)->orderBy('id', 'Desc')->first();
        $data['categories'] = Category::where('user_id', $id_usuario)->get();
        $cat = 0;
        $data['slug'] = $slug;

        if (request()->cat && request()->cat > 0) {
            $cat = request()->cat;
        }

        $total = Product::query()
            ->with('image', 'category')
            ->where('share', 1)
            ->where('user_id', $id_usuario)
            ->when($cat > 0, function ($q) {
                $q->where('category_id', request()->cat);
            })
            ->count();

        $data['pages'] = (int)($total / 4);

        $data['products'] = Product::query()
            ->with('image', 'category')
            ->where('share', 1)
            ->where('user_id', $id_usuario)
            ->when($cat > 0, function ($q) {
                $q->where('category_id', request()->cat);
            })
            ->paginate(10);

        if ($request->ajax()) {
            $view = view('V3.data', $data)->render();
            return response()->json(['html' => $view]);
        }
        // return $data['company']->theme;
        return view('V3.products', $data);
    }
}
