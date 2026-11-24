<?php

namespace App\Core\Features;

use App\Core\Companies\Models\Company;
use App\Core\Features\Events\FeatureDisabled;
use App\Core\Features\Events\FeatureEnabled;
use App\Core\Features\Models\CompanyModule;
use App\Core\Modules\Models\Module;

/**
 * Feature flags por empresa: qué módulos del catálogo global tiene
 * habilitados una empresa concreta.
 */
class FeatureManager
{
    public function enable(Company $company, string $moduleSlug): void
    {
        $module = $this->moduleOrFail($moduleSlug);

        CompanyModule::query()->withoutTenancy()->updateOrCreate(
            ['company_id' => $company->id, 'module_id' => $module->id],
            ['enabled' => true],
        );

        FeatureEnabled::dispatch($company, $module);
    }

    public function disable(Company $company, string $moduleSlug): void
    {
        $module = $this->moduleOrFail($moduleSlug);

        CompanyModule::query()->withoutTenancy()->updateOrCreate(
            ['company_id' => $company->id, 'module_id' => $module->id],
            ['enabled' => false],
        );

        FeatureDisabled::dispatch($company, $module);
    }

    public function enabled(Company $company, string $moduleSlug): bool
    {
        return CompanyModule::query()->withoutTenancy()
            ->where('company_id', $company->id)
            ->whereHas('module', fn ($query) => $query->where('slug', $moduleSlug))
            ->where('enabled', true)
            ->exists();
    }

    public function disabled(Company $company, string $moduleSlug): bool
    {
        return ! $this->enabled($company, $moduleSlug);
    }

    /**
     * Habilita exactamente el conjunto de módulos indicado para la empresa
     * (deshabilita cualquier otro que estuviera habilitado).
     *
     * @param  array<int, string>  $moduleSlugs
     */
    public function sync(Company $company, array $moduleSlugs): void
    {
        $allSlugs = Module::query()->pluck('slug');

        foreach ($allSlugs as $slug) {
            in_array($slug, $moduleSlugs, true)
                ? $this->enable($company, $slug)
                : $this->disable($company, $slug);
        }
    }

    public function isInstalled(string $moduleSlug): bool
    {
        return Module::query()->where('slug', $moduleSlug)->exists();
    }

    private function moduleOrFail(string $moduleSlug): Module
    {
        return Module::query()->where('slug', $moduleSlug)->firstOrFail();
    }
}
