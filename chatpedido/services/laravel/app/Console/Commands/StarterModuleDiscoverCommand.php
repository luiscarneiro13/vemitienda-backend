<?php

namespace App\Console\Commands;

use App\Core\Modules\ModuleManager;
use Illuminate\Console\Command;

class StarterModuleDiscoverCommand extends Command
{
    protected $signature = 'starter:module-discover';

    protected $description = 'Escanea Modules/ y sincroniza el catálogo global de módulos';

    public function handle(ModuleManager $modules): int
    {
        $synced = $modules->discover();

        $this->info(count($synced).' módulo(s) sincronizado(s).');

        return self::SUCCESS;
    }
}
