<?php

namespace App\Core\Features\Models;

use App\Core\Modules\Models\Module;
use App\Core\Support\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Feature flag: representa si un módulo del catálogo global está
 * habilitado para una empresa concreta. La relación company() la provee
 * HasCompany (vía TenantModel).
 */
class CompanyModule extends TenantModel
{
    protected $fillable = [
        'company_id',
        'module_id',
        'enabled',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'configuration' => 'array',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
