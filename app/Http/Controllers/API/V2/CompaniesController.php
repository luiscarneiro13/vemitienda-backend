<?php

namespace App\Http\Controllers\API\V2;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CompanyRequest;
use App\Models\Company;
use App\Models\PlanUser;
use App\Repositories\CompaniesRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CompaniesController extends Controller
{

    use ApiResponser;

    public function index()
    {
        return $this->successResponse(['data' => CompaniesRepository::showCompanyUser()]);
    }

    public function store(CompanyRequest $request)
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();
        $userInfo = User::find($user->id);
        try {
            $datos = array_merge(['email' => $userInfo->email, 'user_id' => $user->id, 'slug' => Str::slug(request()->name, '-')], request()->all());
            if ($company) {
                $company = CompaniesRepository::updateCompany($company->id);
            } else {
                $planUser = PlanUser::where('user_id', $user->id)->first();

                if ($planUser->plan_id == 3) {
                    $is_shop = 1;
                } else {
                    $is_shop = 0;
                }

                $datos = array_merge(['is_shop' => $is_shop], $datos);
                $company = CompaniesRepository::storeCompany($datos);
            }
            return $this->successResponse(['message' => 'Empresa guardada', 'data' => $company]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    public function show($id)
    {
        //
    }

    public function destroy()
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();
        $company->delete();
        return "LIsto";
    }
}
