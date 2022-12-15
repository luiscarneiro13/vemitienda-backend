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

/**
 * @OA\Info(title="API Ve mi Tienda", version="1.0")
 *
 * @OA\Server(url="http://localhost:8000/api/v1")
 */

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


    /**
     * @OA\Post(
     *     path="/login",
     *   tags={"Auth"},
     *     summary="Inicio de Sesión",
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function login()
    {
        info(request()->all());
        $username='';

        if (request()->email) {
            $username = request()->email;
        }

        if (request()->username) {
            $username = request()->email;
        }

        try {
            $user = User::where('email', $username)->first();
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

    public function prueba()
    {
        try {
            $data = User::with(
                'companies',
                'products.category',
                'plans.services',
                'planUser.payments.paymentDetails.paymentMethod'
            )->first();
            return $this->successResponse(['data' => $data]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['error' => $th]);
        }
    }
}
