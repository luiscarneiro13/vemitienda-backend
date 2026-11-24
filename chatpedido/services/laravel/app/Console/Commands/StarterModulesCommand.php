<?php

namespace App\Console\Commands;

use App\Core\Modules\ModuleManager;
use Illuminate\Console\Command;

class StarterModulesCommand extends Command
{
    protected $signature = 'starter:modules';

    protected $description = 'Lista los módulos registrados en el catálogo global';

    public function handle(ModuleManager $modules): int
    {
        $rows = $modules->list()->map(fn ($module) => [
            $module->slug,
            $module->name,
            $module->version ?? '-',
            $module->enabled ? 'sí' : 'no',
        ]);

        if ($rows->isEmpty()) {
            $this->info('No hay módulos registrados. Ejecuta "starter:module-discover" para escanear Modules/.');

            return self::SUCCESS;
        }

        $this->table(['Slug', 'Nombre', 'Versión', 'Habilitado'], $rows);

        return self::SUCCESS;
    }
}
