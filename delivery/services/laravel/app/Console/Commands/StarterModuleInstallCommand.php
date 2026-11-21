<?php

namespace App\Console\Commands;

use App\Core\Modules\ModuleManager;
use Illuminate\Console\Command;

class StarterModuleInstallCommand extends Command
{
    protected $signature = 'starter:module-install {slug}';

    protected $description = 'Instala (registra y habilita globalmente) un módulo detectado en Modules/';

    public function handle(ModuleManager $modules): int
    {
        $module = $modules->install($this->argument('slug'));

        $this->info("Módulo [{$module->name}] instalado.");

        return self::SUCCESS;
    }
}
