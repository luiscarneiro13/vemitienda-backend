<?php

namespace App\Console\Commands;

use App\Core\Companies\Models\Company;
use App\Core\Modules\ModuleManager;
use Illuminate\Console\Command;

class StarterModuleEnableCommand extends Command
{
    protected $signature = 'starter:module-enable {slug} {--company= : Slug de la empresa. Si se omite, habilita el módulo a nivel global}';

    protected $description = 'Habilita un módulo, globalmente o para una empresa concreta';

    public function handle(ModuleManager $modules): int
    {
        $company = $this->resolveCompany();

        if ($this->option('company') && ! $company) {
            $this->error("No existe ninguna empresa con slug [{$this->option('company')}].");

            return self::FAILURE;
        }

        $modules->enable($this->argument('slug'), $company);

        $this->info('Módulo habilitado.');

        return self::SUCCESS;
    }

    private function resolveCompany(): ?Company
    {
        $slug = $this->option('company');

        return $slug ? Company::query()->where('slug', $slug)->first() : null;
    }
}
