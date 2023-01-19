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
    //url="https://whale-app-gd46k.ondigitalocean.app/api/v1/"
    /**
     * @OA\Info(
     *   title="API Ve mi Tienda",
     *   version="1.0",
     *   description="Se inicia sesión, Auth->login, se toma el token y se ingresa arriba en el botón Authorize"
     *   )
     * @OA\Server(
     *  url="https://whale-app-gd46k.ondigitalocean.app/api/v1/"
     * )
     * @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     * )
     * @OA\Tag(
     *   name="Auth",
     *   description="Auth de la aplicación"
     * ),
     * @OA\Tag(
     *   name="Categories",
     *   description="Endpoints de Categorías"
     * )
     */

    public function index()
    {
        return $this->successResponse(['data' => UsersRepository::getUsers()]);
    }


    /**
     * @OA\Get(
     *     path="/user-information",
     *     security={{"bearer_token":{}}},
     *     summary="Mostrar Información del Usuario de la App",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function userInformation()
    {
        $data = UsersRepository::getUserInformation();
        return $this->successResponse(['data' => $data]);
    }


    /**
     * @OA\Post(
     * path="/login",
     * operationId="authLogin",
     * tags={"Auth"},
     * summary="User Login",
     * security={{"bearerAuth":{}}},
     * description="Login User Here",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Datos de la Empresa",
     *        @OA\JsonContent(
     *           required={"email","password"},
     *           @OA\Property(property="email", type="string", format="email", example="administrador@gmail.com"),
     *           @OA\Property(property="password", type="string", format="password", example="123456"),
     *        )
     *     ),
     *      @OA\Response(
     *          response=201,
     *          description="Login Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Login Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function login()
    {
        info(request()->all());
        $username = '';

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

    /**
     * @OA\Post(
     *     tags={"Auth"},
     *     path="/logout",
     *     security={{"bearer_token":{}}},
     *     summary="Desloguear usuario",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        return $this->successResponse();
    }

    /**
     * @OA\Post(
     *     tags={"Auth"},
     *     path="/cancel-account",
     *     security={{"bearer_token":{}}},
     *     summary="Dar de baja al usuario",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function cancelAccount()
    {
        $user = Auth::user();
        try {
            $user->delete();
            return $this->successResponse(['message' => 'Datos guardados', 'data' => $user]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    public function prueba()
    {
        try {
            $data = User::with(
                'company',
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
