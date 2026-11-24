<?php

namespace App\Console\Commands;

use App\Core\Modules\ModuleManager;
use Illuminate\Console\Command;

class StarterModuleRemoveCommand extends Command
{
    protected $signature = 'starter:module-remove {slug}';

    protected $description = 'Elimina un módulo del catálogo global (y sus feature flags por empresa)';

    public function handle(ModuleManager $modules): int
    {
        if (! $this->confirm("¿Eliminar el módulo [{$this->argument('slug')}]? Esta acción no se puede deshacer.")) {
            return self::SUCCESS;
        }

        $modules->remove($this->argument('slug'));

        $this->info('Módulo eliminado.');

        return self::SUCCESS;
    }
}
