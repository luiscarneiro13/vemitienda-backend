<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\LoginRequest;
use App\Http\Resources\API\V1\UserInformationResource;
use App\Repositories\UsersRepository;
use App\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class UserController extends Controller
{

    use ApiResponser, HasApiTokens;

    public function index()
    {
        return $this->successResponse(['data' => UsersRepository::getUsers()]);
    }

    public function userInformation()
    {
        $data = UsersRepository::getUserInformation();
        return $this->successResponse(['data' => $data]);
    }

    public function login()
    {
        try {
            $user = User::where('email', request()->email)->first();
            if (is_object($user)) {
                if ($user->email_verified_at) {
                    if (Hash::check(request()->password, $user->password)) {
                        $user->token = $user->createToken(env('APP_KEY'))->accessToken;
                        $data = $user;
                        return $this->successResponse(['data' => $data]);
                    } else {
                        // return $this->globals->response('forbidden', 'Datos incorrectos');
                    }
                } else {
                    // return $this->globals->response('forbidden', 'Debe activar su cuenta mediante el link de activación enviado a su correo');
                }
            } else {
                // return $this->globals->response('not-found', 'El usuario no existe, por favor debe registrarse');
            }
        } catch (\Throwable $error) {
            return $error;
        }
    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        return $this->successResponse();
    }
}
