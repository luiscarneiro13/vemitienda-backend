<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsuarioRequest;
use App\Models\PlanUser;
use App\Models\Product;
use App\Models\Visit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filtrar = request()->get('query');


        $datos['infoData'] = User::with('products', 'company')
            // ->join('companies', 'users.id', '=', 'companies.user_id')
            ->where('invalid', 0)
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                $q->orWhere('email', 'like', '%' . $filtrar . '%');
                return $q;
            })
            ->orderBy('id', 'desc')
            ->paginate(20);
// return $datos;
        $data['usuariosTotales'] = User::count();
        $data['productosTotales'] = Product::has('image')->count();

        $users = User::whereNotNull('provider_user_id')->select('id')->get();
        $usersArray = $users->pluck('id')->toArray();

        $data['totalUsuariosReales'] = $users->count();
        $data['totalProductosReales'] = Product::has('image')->whereIn('user_id', $usersArray)->get()->count();

        $data['usuariosRealesConProductos'] = User::has('products')->whereIn('id', $usersArray)->count();


        $data['visits'] = Visit::count();

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('Admin.Usuarios.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.Usuarios.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsuarioRequest $request)
    {
        $insert = request()->all();
        $insert['password'] = Hash::make(request()->password);
        $user = User::create($insert);
        $user->save();
        return redirect()->route('usuarios.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('Admin.Usuarios.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['usuario'] = User::find($id);
        return view('Admin.Usuarios.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UsuarioRequest $request, $id)
    {
        $user = User::find($id);
        $user->name        = request()->name;
        $user->email       = request()->email;

        if (!is_null(request()->password)) {
            $user->password = request()->password;
        }

        $user->save();

        return redirect()->route('usuarios.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('usuarios.index');
    }
}
