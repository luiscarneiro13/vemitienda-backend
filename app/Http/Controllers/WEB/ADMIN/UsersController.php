<?php

namespace App\Http\Controllers\WEB\ADMIN;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\UserRequest;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $datos['infoData'] = UsersRepository::getUsers(9);

        $datos['nombreColumnas'] = collect([
            'Nombre' => 'name',
            'Email' => 'email'
        ]);

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('admin.users.index', $data);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        try {
            UsersRepository::storeUser(true);
            $response = [
                'message' => 'Usuario creado',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('users.index')->with($response);
    }

    public function edit($id)
    {
        $data['user'] = UsersRepository::editUser($id);
        return view('admin.users.edit', $data);
    }

    public function update(UserRequest $request, $id)
    {
        try {
            UsersRepository::updateUser($id);
            $response = [
                'message' => 'Usuario actualizado',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('users.index')->with($response);
    }

    public function destroy($id)
    {
        try {
            UsersRepository::deleteUser($id);
            $response = [
                'message' => 'Usuario eliminado',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('users.index')->with($response);
    }
}
