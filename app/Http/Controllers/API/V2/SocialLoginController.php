<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
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
            //code...
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->input('token'));

            $user = User::where('email', $socialUser->email)->first();

            if (!$user) {
                $user = new User();
                $user->name = $socialUser->getName();
                $user->email = $socialUser->getEmail();
                $user->password = Hash::make('lakjasldkj2348723kljhnsdf');
                $user->provider = $provider;
                $user->provider_user_id = $socialUser->getId();
                $user->save();
            }

            Auth::login($user, true);

            $user->token = $user->createToken(env('APP_KEY'))->accessToken;
            $data = $user;
            return $this->successResponse(['data' => $data]);
        } catch (Exception $th) {
            info($th);
            return $this->errorResponse(['error' => $th]);
        }
    }

    // public function handleProviderCallback($provider)
    // {
    //     try {
    //         //code...
    //         $socialUser = Socialite::driver($provider)->user();

    //         $user = User::where('email', request()->email)->first();

    //         if (!$user) {
    //             $user = new User();
    //             $user->name = $socialUser->getName();
    //             $user->email = $socialUser->getEmail();
    //             $user->password= Hash::make('sadlkjhASLKDJ23879287323');
    //             $user->save();
    //         }

    //         $user->token = $user->createToken(env('APP_KEY'))->accessToken;
    //         $data = $user;
    //         return $this->successResponse(['data' => $data]);
    //     } catch (\Throwable $th) {
    //         return $this->errorResponse(['message' => $th]);
    //     }
    // }
}
