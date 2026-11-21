<?php

namespace App\Core\Scopes;

use App\Core\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Filtra automáticamente cualquier modelo que use el trait HasCompany por
 * la empresa activa del TenantContext. super_admin bypassea el scope.
 */
class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        if (Auth::user()->hasRole('super_admin')) {
            return;
        }

        $companyId = app(TenantContext::class)->companyId();

        $builder->where($model->qualifyColumn('company_id'), $companyId);
    }
}
