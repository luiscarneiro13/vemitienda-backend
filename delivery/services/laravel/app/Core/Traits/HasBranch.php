<?php

namespace App\Core\Traits;

use App\Core\Branches\Models\Branch;
use App\Core\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Relaciona el modelo con una sucursal y autoasigna `branch_id` desde el
 * TenantContext al crear, si no viene seteado. A diferencia de HasCompany,
 * no impone un scope global obligatorio: no todos los recursos de una
 * empresa están necesariamente acotados a una sucursal específica.
 */
trait HasBranch
{
    public static function bootHasBranch(): void
    {
        static::creating(function ($model): void {
            if (! $model->branch_id) {
                $model->branch_id = app(TenantContext::class)->branchId();
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
