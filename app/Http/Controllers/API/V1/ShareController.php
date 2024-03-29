<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\PlanUser;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ShareController extends Controller
{

    // Aquí se recibe la solicitud para compartir el catálogo con id usuario desencriptado
    // Entonces encripto al id_usuario y redirijo a la ruta final
    public function init($id_usuario)
    {
        $id_usuario = Crypt::encrypt($id_usuario);
        return redirect(url("share/" . $id_usuario));
    }

    public function share(Request $request, $id_encriptado)
    {

        if (!isset(request()->query) && !isset(request()->cat)) {
            return redirect(url('share/' . $id_encriptado . '?cat=0'));
        }

        $data['cat'] = request()->cat;
        $id_usuario = Crypt::decrypt($id_encriptado);
        $data['id_encriptado'] = $id_encriptado;
        $planUser = PlanUser::where('user_id', $id_usuario)->orderBy('id', 'Desc')->first();
        $data['company'] = Company::with('logo')->where('user_id', $id_usuario)->first();
        $data['categories'] = Category::where('user_id', $id_usuario)->get();

        if ($planUser && $planUser->plan_id == 2) {

            $cat = 0;

            if (request()->query) {
                $total = Product::query()
                    ->with('image', 'category')
                    ->where('share', 1)
                    ->where('user_id', $id_usuario)
                    ->where('name', 'LIKE', '%' . request()->input('query') . '%')
                    ->count();

                $data['pages'] = (int)($total / 4);

                $data['products'] = Product::query()
                    ->with('image', 'category')
                    ->where('share', 1)
                    ->where('user_id', $id_usuario)
                    ->where('name', 'LIKE', '%' . request()->input('query') . '%')
                    ->paginate(30);
            } else {
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
                    ->paginate(5);
            }
        } else {
            $data['products'] = [];
            $data['pages'] = 0;
        }

        if ($request->ajax()) {
            $view = view('share.data', $data)->render();
            return response()->json(['html' => $view]);
        }

        return view('share.index', $data);
    }

    public function shareAPI($id_encriptado)
    {
        $data['cat'] = request()->cat;
        $id_usuario = Crypt::decrypt($id_encriptado);
        $planUser = PlanUser::where('user_id', $id_usuario)->orderBy('id', 'Desc')->first();
        $data['company'] = Company::with('logo')->where('user_id', $id_usuario)->first();
        if ($planUser && $planUser->plan_id == 2) {
            $cat = null;
            if (request()->cat && request()->cat > 0) {
                $cat = request()->cat;
            }
            $data['products'] = Product::query()
                ->with('image', 'category')
                ->where('share', 1)
                ->where('user_id', $id_usuario)
                ->when($cat, function ($q) {
                    $q->where('category_id', request()->cat);
                })
                ->paginate(5);
        } else {
            $data['products'] = [];
        }

        return response()->json($data);
    }
}
