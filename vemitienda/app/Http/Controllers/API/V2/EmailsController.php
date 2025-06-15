<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\User;
use Illuminate\Http\Request;

class EmailsController extends Controller
{
    // Se envía un email ofreciendo soporte y dando los medios de contacto
    public function soporte()
    {
        $id = request()->user_id;
        $user = User::find($id);
        $resp = null;

        if ($user) {
            $resp = $this->emailUser($user);
        } else {
            $resp = "No existe el usuario";
        }

        return response()->json($resp);
    }

    public function emailUser($user)
    {
        $parametros['name'] = $user->name;
        $parametros['destinatario'] = $user->email;
        $parametros['type'] = 'EmailSoporte';

        dispatch(new SendEmailJob($parametros));

        return "Se envió el correo";
    }
}
