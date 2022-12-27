<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompaniesRepository
{
    static function getCompanies($limit = 10)
    {
        $filtrar = request()->get('query');

        return Company::query()
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                $q->orWhere('slogan', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);
    }

    static function storeCompany($insert)
    {
        return Company::create($insert);
    }

    static function showCompanyUser()
    {
        $user = Auth::user();
        return Company::where('user_id', $user->id)->first();
    }

    static function editCompany($id)
    {
        return Company::find($id);
    }

    static function updateCompany($id)
    {
        $model = Company::find($id);
        $model->name = request()->name;
        $model->slogan = request()->slogan;
        $model->email = request()->email;
        $model->phone = request()->phone;
        return $model->save();
    }

    static function deleteCompany($id)
    {
        $model = Company::find($id);
        return $model->delete();
    }
}
