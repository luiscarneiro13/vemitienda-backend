<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    use ApiResponser;

    public function handleProviderCallback(Request $request, $provider, User $userModel)
    {
        $providerUser = Socialite::driver($provider)->userFromToken($request->input('access_token'));
        $user = $userModel->firstOrCreate(
            ['provider_user_id' => $providerUser->id],
            [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make('lkjasdlkj98729834oiHJHJAuiywermnqwe76'),
                'provider_user_id' => $providerUser->id,
            ]
        );
        $user->token = $user->createToken(env('APP_KEY'))->accessToken;
        return $this->successResponse(['data' => $user]);
    }
}
