<?php

namespace App\Core\Modules;

use App\Core\Modules\Models\Module;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Registra en el contenedor el Service Provider de cada módulo habilitado
 * en el catálogo global. Se ejecuta en el boot de la aplicación.
 */
class ModuleLoader
{
    public function __construct(private readonly Application $app) {}

    public function loadEnabled(): void
    {
        // Antes del primer `migrate` (instalación nueva) la tabla `modules`
        // todavía no existe: no romper el boot de la aplicación por eso.
        if (! Schema::hasTable('modules')) {
            return;
        }

        Module::query()->where('enabled', true)->get()->each(
            fn (Module $module) => $this->loadProvider($module)
        );
    }

    private function loadProvider(Module $module): void
    {
        if (! $module->provider) {
            return;
        }

        if (! class_exists($module->provider)) {
            Log::warning("Módulo [{$module->slug}]: el provider [{$module->provider}] no existe.");

            return;
        }

        $this->app->register($module->provider);
    }
}
