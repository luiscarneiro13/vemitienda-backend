<?php

namespace App\Core\Modules;

use App\Core\Companies\Models\Company;
use App\Core\Features\FeatureManager;
use App\Core\Modules\Events\ModuleInstalled;
use App\Core\Modules\Events\ModuleRemoved;
use App\Core\Modules\Models\Module;
use Illuminate\Support\Collection;

/**
 * Fachada de alto nivel sobre ModuleRegistry (discovery) y FeatureManager
 * (activación por empresa). Punto de entrada usado por los comandos
 * artisan `starter:module-*`.
 */
class ModuleManager
{
    public function __construct(
        private readonly ModuleRegistry $registry,
        private readonly FeatureManager $features,
    ) {}

    public function discover(): array
    {
        return $this->registry->sync();
    }

    public function install(string $slug): Module
    {
        $this->registry->sync();

        $module = Module::query()->where('slug', $slug)->firstOrFail();
        $module->update(['enabled' => true]);

        ModuleInstalled::dispatch($module);

        return $module;
    }

    public function enable(string $slug, ?Company $company = null): void
    {
        if ($company) {
            $this->features->enable($company, $slug);

            return;
        }

        Module::query()->where('slug', $slug)->firstOrFail()->update(['enabled' => true]);
    }

    public function disable(string $slug, ?Company $company = null): void
    {
        if ($company) {
            $this->features->disable($company, $slug);

            return;
        }

        Module::query()->where('slug', $slug)->firstOrFail()->update(['enabled' => false]);
    }

    public function remove(string $slug): void
    {
        Module::query()->where('slug', $slug)->firstOrFail()->delete();

        ModuleRemoved::dispatch($slug);
    }

    /**
     * @return Collection<int, Module>
     */
    public function list(): Collection
    {
        return Module::all();
    }

    public function isInstalled(string $slug): bool
    {
        return $this->features->isInstalled($slug);
    }
}
