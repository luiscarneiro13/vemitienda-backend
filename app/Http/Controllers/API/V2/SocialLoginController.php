<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            //code...
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('provider_user_id', $socialUser->getId())->first();

            if (!$user) {
                $user = new User();
                $user->name = $socialUser->getName();
                $user->email = $socialUser->getEmail();
                $user->provider = $provider;
                $user->provider_user_id = $socialUser->getId();
                $user->save();
            }

            $user->token = $user->createToken('Social Login')->accessToken;
            $data = $user;
            return $this->successResponse(['data' => $data]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }
}
