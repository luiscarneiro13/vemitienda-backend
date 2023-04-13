<?php

namespace App\Http\Controllers\API\V2;

use App\Helpers\Images;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CompanyRequest;
use App\Models\Company;
use App\Repositories\CompaniesRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
     *     summary="Si la Empresa no existe, la crea y si la empresa existe entonces la actualiza",
     *     @OA\RequestBody(
     *        required=true,
     *        description="Datos de la Empresa",
     *        @OA\JsonContent(
     *           required={"email","name","slogan","phone"},
     *           @OA\Property(property="email", type="string", format="email", example="sistelconet@gmail.com"),
     *           @OA\Property(property="name", type="string", format="name", example="Sistelconet El Tigre"),
     *           @OA\Property(property="slogan", type="string", format="slogan", example="Tu Solución en Sistemas"),
     *           @OA\Property(property="phone", type="string", format="phone", example="+584248807465"),
     *           @OA\Property(property="theme_id", type="integer", format="phone", example=1),
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
        $company = Company::where('user_id', $user->id)->first();
        $userInfo = User::find($user->id);
        try {
            $datos = array_merge(['email' => $userInfo->email, 'user_id' => $user->id, 'slug' => Str::slug(request()->name, '-')], request()->all());
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

    /**
     * @OA\Delete(
     *     tags={"Company"},
     *     path="/company/{id}",
     *     security={{"bearer_token":{}}},
     *     summary="Borrar categoría de un Usuario App",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *      ),
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
    public function destroy()
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();
        $company->delete();
        return "LIsto";
    }
}
