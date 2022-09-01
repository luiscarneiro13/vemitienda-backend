<?php

namespace App\Http\Controllers\WEB\ADMIN;

use App\Http\Controllers\Controller;
use App\Http\Requests\WEB\PlanRequest;
use App\Repositories\PlansRepository;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index()
    {
        $datos['infoData'] = PlansRepository::getPlans(9);

        $datos['nombreColumnas'] = collect([
            'Nombre' => 'name',
        ]);

        $datos['token'] = csrf_token();

        $data['data'] = $datos;

        return view('admin.plans.index', $data);
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(PlanRequest $request)
    {
        try {
            PlansRepository::storePlan(true);
            $response = [
                'message' => 'Plan creado',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('plans.index')->with($response);
    }

    public function edit($id)
    {
        $data['plan'] = PlansRepository::editPlan($id);
        return view('admin.plans.edit', $data);
    }

    public function update(PlanRequest $request, $id)
    {
        try {
            PlansRepository::updatePlan($id);
            $response = [
                'message' => 'Plan actualizado',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('plans.index')->with($response);
    }

    public function destroy($id)
    {
        try {
            PlansRepository::deletePlan($id);
            $response = [
                'message' => 'Plan eliminado',
                'alert-type' => 'success',
            ];
        } catch (\Throwable $th) {
            $response = [
                'message' => 'Ocurrió un error: ' . $th->getMessage(),
                'alert-type' => 'error',
            ];
        }

        return redirect()->route('plans.index')->with($response);
    }
}
