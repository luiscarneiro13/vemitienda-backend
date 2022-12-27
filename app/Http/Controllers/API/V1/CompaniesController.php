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

class CompaniesController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->image = new Images();
    }


    /**
     * @OA\Get(
     *     tags={"Company"},
     *     path="/company-user",
     *     security={{"bearer_token":{}}},
     *     summary="Mostrar los datos de la Empresa del Usuario de la App",
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function index()
    {
        return $this->successResponse(['data' => CompaniesRepository::showCompanyUser()]);
    }

    /**
     * @OA\Post(
     *     tags={"Company"},
     *     path="/company-user",
     *     security={{"bearer_token":{}}},
     *     summary="Crear Empresa de un Usuario App",
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *         @OA\Schema(
     *           @OA\Property(
     *             property="name",
     *             description="Nombre de la Empresa",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="slogan",
     *             description="Slogan de la Empresa",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="email",
     *             description="Email de la Empresa",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="phone",
     *             description="Teléfono de la Empresa",
     *             type="string",
     *           ),
     *           @OA\Property(
     *             property="logo",
     *             description="Logotipo de la Empresa",
     *             type="string",
     *           ),
     *         ),
     *       ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exitoso"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function store(CompanyRequest $request)
    {
        try {
            $user = Auth::user();

            $datos = array_merge(['user_id' => $user->id], request()->all());
            $company = CompaniesRepository::storeCompany($datos);

            if (request()->logo) {
                $urlLogo = $this->image->uploadImage('logo', 'logos', 'do');
                $company->logo()->create(['url' => $urlLogo]);
            }
        } catch (\Exception $th) {
            return $this->errorResponse(['message' => $th]);
        }

        return $this->successResponse(['message' => 'Datos guardados', 'data' => $datos]);
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
