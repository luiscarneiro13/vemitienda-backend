<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Repositories\CompaniesRepository;
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

    public function index()
    {
        return $this->successResponse(['data' => CompaniesRepository::showCompanyUser()]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        try {

            $datos = array_merge(['user_id' => $user->id], request()->all());
            $company = Company::updateOrCreate($datos);

            if (request()->logo) {
                $urlLogo = $this->image->uploadImage('logo', 'logos', 'do');
                $company->logo()->create(['url' => $urlLogo]);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }

        return $this->successResponse(['message' => 'Empresa creada con éxito']);
    }

    public function show($id)
    {
        return $this->successResponse(['data' => CompaniesRepository::getCategories(-1)]);
    }

    public function update(Request $request, $id)
    {
        //
    }
    public function destroy($id)
    {
        //
    }
}
