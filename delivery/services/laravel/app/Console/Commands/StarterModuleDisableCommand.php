<?php

namespace App\Console\Commands;

use App\Core\Companies\Models\Company;
use App\Core\Modules\ModuleManager;
use Illuminate\Console\Command;

class StarterModuleDisableCommand extends Command
{
    protected $signature = 'starter:module-disable {slug} {--company= : Slug de la empresa. Si se omite, deshabilita el módulo a nivel global}';

    protected $description = 'Deshabilita un módulo, globalmente o para una empresa concreta';

    public function handle(ModuleManager $modules): int
    {
        $company = $this->resolveCompany();

        if ($this->option('company') && ! $company) {
            $this->error("No existe ninguna empresa con slug [{$this->option('company')}].");

            return self::FAILURE;
        }

        $modules->disable($this->argument('slug'), $company);

        $this->info('Módulo deshabilitado.');

        return self::SUCCESS;
    }

    private function resolveCompany(): ?Company
    {
        $slug = $this->option('company');

        return $slug ? Company::query()->where('slug', $slug)->first() : null;
    }
}
