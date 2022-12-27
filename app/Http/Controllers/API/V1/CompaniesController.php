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
     *        required=true,
     *        description="Datos de la Empresa",
     *        @OA\JsonContent(
     *           required={"email","name","slogan","phone"},
     *           @OA\Property(property="email", type="string", format="email", example="sistelconet@gmail.com"),
     *           @OA\Property(property="name", type="string", format="name", example="Sistelconet"),
     *           @OA\Property(property="slogan", type="string", format="slogan", example="Tu Solución en Sistemas"),
     *           @OA\Property(property="phone", type="string", format="phone", example="+584248807465"),
     *        ),
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
        $user = Auth::user();
        try {
            $datos = array_merge(['user_id' => $user->id], request()->all());
            $company = CompaniesRepository::storeCompany($datos);
            return $this->successResponse(['message' => 'Datos guardados', 'data' => $datos]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    public function show($id)
    {
        //
    }


    /**
     * @OA\Put(
     *     tags={"Company"},
     *     path="/company-user/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Crear Empresa de un Usuario App",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        description="Datos de la Empresa",
     *        @OA\JsonContent(
     *           required={"email","name","slogan","phone"},
     *           @OA\Property(property="email", type="string", format="email", example="sistelconet@gmail.com"),
     *           @OA\Property(property="name", type="string", format="name", example="Sistelconet"),
     *           @OA\Property(property="slogan", type="string", format="slogan", example="Tu Solución en Sistemas"),
     *           @OA\Property(property="phone", type="string", format="phone", example="+584248807465")
     *        )
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
    public function update(Request $request, $id)
    {
        try {
            return $this->successResponse(['message' => 'Datos guardados', 'data' =>  CompaniesRepository::updateCompany($id)]);
        } catch (\Throwable $th) {
            return $this->errorResponse(['message' => $th]);
        }
    }

    public function destroy($id)
    {
        //
    }
}
