<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CompaniesRepository
{
    static function getCompanies($limit = 10)
    {
        $filtrar = request()->get('query');

        return Company::with('theme')
            ->when($filtrar, function ($q) use ($filtrar) {
                $q->where('name', 'like', '%' . $filtrar . '%');
                $q->orWhere('slogan', 'like', '%' . $filtrar . '%');
                return $q;
            })->paginate($limit);
    }

    static function storeCompany($insert)
    {
        $insert['slug'] = Str::slug(request()->name, '-');
        return Company::create($insert);
    }

    static function showCompanyUser()
    {
        $user = Auth::user();
        return Company::with('user', 'logo', 'theme')->where('user_id', $user->id)->first();
    }

    static function editCompany($id)
    {
        return Company::find($id);
    }

    static function updateCompany($id)
    {
        $user = Auth::user();
        $model = Company::find($id);
        $model->user_id = $user->id;
        $model->name = request()->name;
        $model->slug = Str::slug(request()->name, '-');
        $model->theme_id = request()->theme_id;
        $model->slogan = request()->slogan;
        $model->phone = request()->phone;
        $model->background_color_catalog = request()->background_color_catalog;

        if (request()->onboarding) {
            $model->onboarding = request()->onboarding;
        }

        $model->save();
        return $model;
    }

    static function deleteCompany($id)
    {
        $model = Company::find($id);
        return $model->delete();
    }
}
