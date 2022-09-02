<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->image = new Images();
    }

    public function storeCompanyUser(Request $request)
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();
        if (!is_object($company)) {
            try {

                $data = [
                    "user_id" => $user->id,
                    "name" => request()->name,
                    "slogan" => request()->slogan || '',
                    "email" => $user->email,
                    "phone" => request()->phone,
                ];

                $company = Company::create($data);
                $company->save();
                $urlLogo = $this->image->uploadImage('logo', 'logos', 'do');
                $company->logo()->create(['url' => $urlLogo]);
            } catch (\Throwable $th) {
                return $this->errorResponse(['message' => $th]);
            }
        } else {
            return $this->errorResponse(['message' => 'El usuario ya tiene una empresa creada']);
        }
        return $this->successResponse(['message' => 'Empresa creada con éxito']);
    }
}
