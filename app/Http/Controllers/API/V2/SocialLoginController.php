<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\Hash;

class SocialLoginController extends Controller
{

    use ApiResponser;

    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
            // $user = User::where('email', request()->email)->first();

            if (!$user) {
                $user = new User();
                $user->name = request()->name;
                $user->email = request()->email;
                $user->password= Hash::make('lkjasdlkj98729834oiHJHJAuiywermnqwe76');
                $user->save();
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
