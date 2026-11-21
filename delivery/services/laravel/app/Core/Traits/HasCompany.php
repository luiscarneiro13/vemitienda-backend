<?php

namespace App\Core\Traits;

use App\Core\Companies\Models\Company;
use App\Core\Scopes\CompanyScope;
use App\Core\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Aísla el modelo por empresa: aplica el CompanyScope automáticamente y
 * autoasigna `company_id` en creating desde el TenantContext si no viene
 * seteado. El desarrollador nunca debe escribir `where('company_id', ...)`
 * a mano — este trait es la única puerta de entrada.
 */
trait HasCompany
{
    public static function bootHasCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model): void {
            if (! $model->company_id) {
                $model->company_id = app(TenantContext::class)->companyId();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeWithoutTenancy(Builder $query): Builder
    {
        return $query->withoutGlobalScope(CompanyScope::class);
    }
}
