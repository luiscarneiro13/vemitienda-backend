<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
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

    public function share($id_encriptado)
    {
        $id_usuario = Crypt::decrypt($id_encriptado);
        // $data['catalog'] = Product::where('user_id', $id_usuario)->where('share', 1)->get();
        $data['company'] = Company::with('logo')->where('user_id', $id_usuario)->first();
        $data['categories'] = Category::with('products')->where('user_id', $id_usuario)->get();
        return view('share.index', $data);
    }
}
