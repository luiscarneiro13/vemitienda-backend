<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CorreccionesController extends Controller
{
    public function asignarNombreTienda()
    {
        $companies = Company::orderBy('id', 'desc')->get();

        $i = 0;
        foreach ($companies as $company) {
            $i++;
            if (!$company->name) {
                $name = 'tienda ' . $i;
                $slug = Str::slug($name, '-');
                $this->storeCompany($name, $slug, $company);
            }
        }
    }

    public function storeCompany($name, $slug, $company)
    {
        $company->name = $name;
        $company->slug = $slug;
        $company->theme_id = 1;
        $company->background_color_catalog = '#FFFFFF';
        $company->is_shop = 1;
        $company->save();
    }
}
