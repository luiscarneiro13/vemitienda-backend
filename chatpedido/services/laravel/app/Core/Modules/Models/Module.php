<?php

namespace App\Core\Modules\Models;

use App\Core\Support\CoreModel;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Catálogo global de módulos detectados por el ModuleRegistry. No indica
 * si un módulo está habilitado para una empresa concreta — eso vive en
 * `company_modules` (ver CompanyModule / FeatureManager).
 */
class Module extends CoreModel
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'provider',
        'enabled',
        'dependencies',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'dependencies' => 'array',
            'configuration' => 'array',
        ];
    }

    public function companyModules(): HasMany
    {
        return $this->hasMany(CompanyModule::class);
    }
}
