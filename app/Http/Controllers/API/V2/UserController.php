<?php

namespace App\Http\Controllers\API\V2;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Repositories\UsersRepository;
use App\Http\Requests\API\V1\LoginRequest;
use App\Http\Requests\API\V1\RegisterRequest;
use App\Http\Requests\API\V1\ResetPasswordRequest;
use App\Http\Requests\API\V1\ResetRequest;
use App\Http\Resources\API\V1\UserInformationResource;
use App\Jobs\SendEmailJob;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\PlanUser;
use Carbon\Carbon;

class UserController extends Controller
{

    use ApiResponser, HasApiTokens;
    //url="https://whale-app-gd46k.ondigitalocean.app/api/v1/"
    //url="https://vemitiendabackend.tests/api/v1/"
    /**
     * @OA\Info(
     *   title="API Ve mi Tienda",
     *   version="1.0",
     *   description="Se inicia sesión, Auth->login, se toma el token y se ingresa arriba en el botón Authorize"
     *   )
     * @OA\Server(
     *  url="https://vemitienda.online/api/v2/"
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

    public function login(LoginRequest $request)
    {
        try {
            $user = User::with('planUser')->where('email', request()->email)->first();
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

    public function register(RegisterRequest $request)
    {

        try {
            $user = User::create([
                'name'     => request()->name,
                'email'    => request()->email,
                'password' => Hash::make(request()->password)
            ]);
            $user->save();
            $this->emailWellcome($user);

            return $this->successResponse([
                'data' => $user,
                'message' => 'Registro exitoso, le enviamos un email para que confirme su cuenta'
            ]);
        } catch (Exception $th) {
            return $this->errorResponse(['error' => $th]);
        }
    }

    public function emailWellcome($user)
    {
        /* 1.- Se encripta el id del usuario y se pasa a la ruta web confirmationUser/asdkljasldkjlkjeouweoiru*/
        $user_id = Crypt::encrypt($user->id);
        /* 2.- Se envía correo */
        $parametros['name'] = $user->name;
        $parametros['destinatario'] = $user->email;
        $parametros['url'] = url('confirmationuser/' . $user_id);
        $parametros['type'] = 'ActivarCuenta';

        dispatch(new SendEmailJob($parametros));
    }

    public function confirmationuser($encriptado)
    {
        /* 1.- Se desecripta el id */
        $user_id = Crypt::decrypt($encriptado);
        /* 2.- Si el usuario no había verificado antes, lo verifico. Sino no hago más nada */
        $user = User::find($user_id);
        if ($user) {
            if (is_null($user->email_verified_at)) {
                /* 2.1.- Verificar email del usuario*/
                $user->email_verified_at = now();
                $user->save();
                /* 2.1.- Asignarle el plan premium */
                $plan = Plan::where('name', 'Tienda Online')->first();

                $planUser = PlanUser::create([
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                    'activo' => 1,
                    'start_date' => Carbon::parse(now())->format('Y-m-d H:i:s'),
                    'end_date' => Carbon::parse(now())->addDays(30)->format('Y-m-d H:i:s'),
                ]);
                $planUser->save();

                return redirect('/message')->with('message', 'Cuenta activada con éxito');
            } else {
                return redirect('/message')->with('message', 'Cuenta activada previamente');
            }
        } else {
            return redirect('/message')->with('message', 'Ocurrió un problema inesperado');
        }
    }

    public function message()
    {
        return view('Mensajes');
    }

    public function searchEmail()
    {
        $user = User::where('email', request()->email)->first();
        if ($user) {
            return $this->successResponse(['data' => $user]);
        } else {
            return $this->errorResponse(['error' => 'Datos inválidos']);
        }
    }

    public function reset1()
    {
        $user_id = Crypt::encrypt(request()->user_id);
        $user = User::find(request()->user_id);
        /* 2.- Se envía correo */
        $parametros['name'] = $user->name;
        $parametros['destinatario'] = $user->email;
        $parametros['url'] = url('reset2/' . $user_id);
        $parametros['type'] = 'RecuperarCuenta';

        dispatch(new SendEmailJob($parametros));

        return $this->successResponse(['message' => 'Enviamos un email con las instrucciones para recuperar su contraseña']);
    }

    public function reset2($user_id)
    {
        $user = Crypt::decrypt($user_id);
        $data['user_id'] = $user;
        return view('auth.ResetPassword', $data);
    }

    public function reset3(ResetRequest $request)
    {
        $user = User::find(request()->user_id);
        if ($user) {
            $user->password = Hash::make(request()->password);
            $user->save();
            return redirect()->route('message')->with('message', '¡Contraseña cambiada con éxito!');
        } else {
            //datos inválidos
            return redirect()->route('message')->with('message', '¡Datos inválidos!');
        }
    }
}