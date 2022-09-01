<?php

namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\Hash;

class UsersRepository
{
    static function getUsers($limit = 10)
    {
        $filtrar = request()->get('query');

        return User::query()
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                $q->orWhere('email', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);
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
