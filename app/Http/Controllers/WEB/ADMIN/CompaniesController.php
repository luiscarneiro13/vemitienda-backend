<?php

namespace App\Http\Controllers\WEB\ADMIN;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\CompanyRequest;
use App\Repositories\CompaniesRepository;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function index()
    {
        $datos['infoData'] = CompaniesRepository::getCompanies(9);

        $datos['nombreColumnas'] = collect([
            'Nombre' => 'name',
            'Slogan' => 'slogan',
            'Email' => 'email',
            'Teléfono' => 'phone',
        ]);

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('admin.companies.index', $data);
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(CompanyRequest $request)
    {
        try {
            CompaniesRepository::storeCompany(true);
            $response = [
                'message' => 'Categoría creada',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('companies.index')->with($response);
    }

    public function edit($id)
    {
        $data['company'] = CompaniesRepository::editCompany($id);
        return view('admin.companies.edit', $data);
    }

    public function update(CompanyRequest $request, $id)
    {
        try {
            CompaniesRepository::updateCompany($id);
            $response = [
                'message' => 'Categoría actualizada',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('companies.index')->with($response);
    }

    public function destroy($id)
    {
        try {
            CompaniesRepository::deleteCompany($id);
            $response = [
                'message' => 'Categoría eliminada',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('companies.index')->with($response);
    }
}
