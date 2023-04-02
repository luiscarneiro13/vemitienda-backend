<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CompanyRequest;
use App\Models\Company;
use App\Repositories\CompaniesRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
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
        try {
            $datos = array_merge(['user_id' => $user->id, 'slug' => Str::slug(request()->name, '-')], request()->all());
            if ($company) {
                $company = CompaniesRepository::updateCompany($company->id);
            } else {
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
