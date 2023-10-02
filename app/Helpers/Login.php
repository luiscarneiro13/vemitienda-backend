<?php

namespace App\Helpers;

use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Login
{
    use ApiResponser, HasApiTokens;

    public function LoginUser($user){
        if (is_object($user)) {
            if ($user->email_verified_at) {
                if (Hash::check(request()->password, $user->password)) {
                    $user->token = $user->createToken(env('APP_KEY'))->accessToken;
                    $data = $user;
                    return $this->successResponse(['data' => $data]);
                } else {
                    return $this->errorValidation([
                        "email" => ["Datos incorrectos"],
                        "message" => ["Datos erróneos"]
                    ]);
                }
            } else {
                return $this->errorValidation([
                    "email" => ["Debe verificar su cuenta"],
                    "message" => ["Datos erróneos"]
                ]);
            }
        } else {
            return $this->errorValidation([
                "email" => ["El usuario no existe, por favor debe registrarse"],
                "message" => ["Datos erróneos"]
            ]);
        }
    }
}
