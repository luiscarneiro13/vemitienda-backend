<?php

namespace App\Repositories;

use App\Models\Plan;

class PlansRepository
{
    static function getPlans($limit = 10)
    {
        $filtrar = request()->get('query');

        return Plan::query()
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);
    }

    static function storePlan($web = false)
    {
        $insert = [
            'name' => request()->name
        ];

        return Plan::updateOrCreate($insert);
    }

    static function editPlan($id)
    {
        return Plan::find($id);
    }

    static function updatePlan($id)
    {
        $model = Plan::find($id);
        $model->name        = request()->name;

        return $model->save();
    }

    static function deletePlan($id)
    {
        $model = Plan::find($id);
        return $model->delete();
    }
}
