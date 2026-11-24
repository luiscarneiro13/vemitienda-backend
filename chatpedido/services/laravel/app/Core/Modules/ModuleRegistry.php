<?php

namespace App\Core\Modules;

use App\Core\Modules\Models\Module;
use Illuminate\Support\Facades\File;

/**
 * Detecta automáticamente los módulos instalados escaneando la carpeta
 * `Modules/` (raíz del proyecto): cada subcarpeta con un `module.json`
 * es un módulo. Nunca se registran módulos a mano — este es el único
 * punto de entrada.
 *
 * Formato esperado de module.json:
 * {
 *   "name": "Billing",
 *   "slug": "billing",
 *   "version": "1.0",
 *   "description": "",
 *   "provider": "Modules\\Billing\\BillingServiceProvider",
 *   "dependencies": []
 * }
 */
class ModuleRegistry
{
    public function __construct(private readonly string $modulesPath = '') {}

    public function path(): string
    {
        return $this->modulesPath ?: base_path('Modules');
    }

    /**
     * Escanea el filesystem y devuelve los manifiestos crudos encontrados,
     * indexados por slug. No toca la base de datos.
     *
     * @return array<string, array>
     */
    public function discover(): array
    {
        $path = $this->path();

        if (! File::isDirectory($path)) {
            return [];
        }

        $manifests = [];

        foreach (File::directories($path) as $moduleDir) {
            $manifestPath = $moduleDir.DIRECTORY_SEPARATOR.'module.json';

            if (! File::exists($manifestPath)) {
                continue;
            }

            $manifest = json_decode(File::get($manifestPath), true);

            if (! is_array($manifest) || empty($manifest['slug'])) {
                continue;
            }

            $manifests[$manifest['slug']] = $manifest;
        }

        return $manifests;
    }

    /**
     * Sincroniza el catálogo global `modules` con lo detectado en el
     * filesystem (upsert por slug). Los módulos que ya no existen en
     * disco no se borran automáticamente — se conservan como registro
     * histórico y quedan disponibles para desinstalación explícita.
     *
     * @return array<int, Module>
     */
    public function sync(): array
    {
        $synced = [];

        foreach ($this->discover() as $slug => $manifest) {
            $synced[] = Module::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $manifest['name'] ?? $slug,
                    'description' => $manifest['description'] ?? null,
                    'version' => $manifest['version'] ?? null,
                    'provider' => $manifest['provider'] ?? null,
                    'dependencies' => $manifest['dependencies'] ?? [],
                    'configuration' => $manifest['configuration'] ?? [],
                ]
            );
        }

        return $synced;
    }
}
