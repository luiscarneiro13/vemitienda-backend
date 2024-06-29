<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\PlanUser;
use App\Traits\ApiResponser;
use App\User;
use Carbon\Carbon;
use Exception;
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
        // info(json_encode($request->all()));
        try {

            $token = request()->access_token;

            $providerUser = Socialite::driver($provider)->userFromToken($token);
            // info(json_encode($providerUser));

            $user = User::with('company')->where('provider_user_id', $providerUser->id)->first();
            // info(json_encode($user));

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
                    $user->email_verified_at = now();
                    $user->password = Hash::make('lkjasdlkj98729834oiHJHJAuiywermnqwe76');
                    $user->provider_user_id = $providerUser->id;
                    $user->save();

                    // Le creo la tienda de una vez
                    $company = Company::create([
                        "user_id" => $user->id,
                        "is_shop" => 1,
                        "email" => $user->email,
                        "background_color_catalog" => '#FFFFFF'
                    ]);
                    $company->save();

                    $user = User::with('company')->where('email', $user->email)->first();
                }
            }

            info(json_encode(["antes" => $user]));
            $user->token = $user->createToken(env('APP_KEY'))->accessToken;
            info(json_encode(["despues" => $user]));
            $data = $user;
            return $this->successResponse(['data' => $data]);
        } catch (Exception $th) {
            info(json_encode(["error" => $th]));
            return $this->errorResponse(['status' => 400, 'message' => 'Error al tratar de acceder']);
        }
    }
}
