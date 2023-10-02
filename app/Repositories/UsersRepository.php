<?php

namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersRepository
{

    static function getUserLogin()
    {
        return User::with('planUser')->where('email', request()->email)->first();
    }

    static function getUsers($limit = 10, $relations = [])
    {
        $filtrar = request()->get('query');

        $datos = User::query()
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                $q->orWhere('email', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);

        if ($relations) {
            $datos = $datos->with($relations);
        }

        return $datos;
    }

    static function getUserInformation()
    {
        $user = Auth::user();
        return User::with([
            'company', 'products.image', 'products.category',
            'products' => function ($q) {
                $q->orderBy('name', 'asc');
            }
        ])
            ->where('id', $user->id)->first();
    }

    static function storeUser($web = false)
    {
        $insert = [
            'name' => request()->name,
            'email' => request()->email,
            'password' => Hash::make(request()->password),
        ];

        if ($web) {
            $insert['email_verified_at'] = now();
        }

        return User::updateOrCreate($insert);
    }

    static function editUser($id)
    {
        return User::find($id);
    }

    static function updateUser($id)
    {
        $model = User::find($id);

        $model->name        = request()->name;
        $model->email       = request()->email;

        if (!is_null(request()->password)) {
            $model->password = request()->password;
        }

        return $model->save();
    }

    static function deleteUser($id)
    {
        $model = User::find($id);
        return $model->delete();
    }
}
