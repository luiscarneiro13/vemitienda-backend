<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class SocialLoginController extends Controller
{
    use ApiResponser;

    public function handleProviderCallback(Request $request, $provider)
    {
        try {

            $token = request()->access_token;

            $providerUser = Socialite::driver($provider)->userFromToken($token);

            $user = User::where('provider_user_id', $providerUser->id)->first();

            // Si no existe el proveedor dentro de ningún usuario
            if (!$user) {

                // Busco al usuario por su email
                $user = User::where('email', request()->email)->first();

                // Si lo encuentro, actualizo su perfil
                if ($user) {
                    $user->provider_user_id = $providerUser->id;
                    $user->save();
                } else { // Si no lo encuentro, creo el usuario
                    $user = new User();
                    $user->name = request()->name;
                    $user->email = request()->email;
                    $user->password = Hash::make('lkjasdlkj98729834oiHJHJAuiywermnqwe76');
                    $user->provider_user_id = $providerUser->id;
                    $user->save();
                }
            }

            $user->token = $user->createToken(env('APP_KEY'))->accessToken;
            $data = $user;
            return $this->successResponse(['data' => $data]);
        } catch (Exception $th) {
            info($th);
            return $this->errorResponse(['error' => $th]);
        }
    }
}
