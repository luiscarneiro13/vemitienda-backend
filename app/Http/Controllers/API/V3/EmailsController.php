<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\User;
use Illuminate\Http\Request;

class EmailsController extends Controller
{
    // Se envía un email ofreciendo soporte y dando los medios de contacto
    public function soporte()
    {
        // $user=User::find(204);
        // return $this->emailUser($user);
        $limit = request()->limit;
        $resp = [];

        $users = User::where('invalid', 0)->where('marketing', 0)->orderBy('id', 'desc')->take($limit)->get();

        if (count($users) > 0) {
            foreach ($users as $user) {
                $user = User::find($user->id);
                if ($user) {
                    $resp[] = $this->emailUser($user);
                    $user->marketing = 1;
                    $user->save();
                } else {
                    $resp = "No existe el usuario";
                }
            }
        }
        return response()->json($resp);
    }

    public function emailUser($user)
    {
        $parametros['name'] = $user->name ?? '';
        $parametros['destinatario'] = $user->email;
        $parametros['type'] = 'EmailSoporte';

        dispatch(new SendEmailJob($parametros));

        return $user->email;
    }
}
