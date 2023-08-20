<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\User;
use Illuminate\Http\Request;

class ControlesController extends Controller
{

    // Este endpoint agrega compañias a los usuarios que no la tienen
    public function addCompanyUser()
    {
        // 1.- Se arma una array con todos los usuarios de prueba (Para excluirlos luego)
        $testUsers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // 2.- Se arma un array con los id de usuarios de las empresas que ya existen y no son de pruebas
        $dataCompanies = Company::whereNotIn('user_id', $testUsers)->pluck('user_id');

        // 3.- Se traen todos los usuarios que no estén en el arrayu anterior
        $users = User::whereNotIn('id', $dataCompanies)->whereNotIn('id', $testUsers)->get();

        foreach ($users as $item) {
            $company = Company::create([
                'user_id' => $item->id,
                'is_shop' => 1,
                'email' => $item->email,
                'background_color_catalog' => '#FFFFFF'
            ]);
            $company->save();
        }

        return "Listo";
    }

    // Asignar plan de tienda a usuarios
}
