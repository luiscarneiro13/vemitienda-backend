<?php

namespace App\Console\Commands;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Actions\EnsureDefaultCatalogAction;
use Illuminate\Console\Command;

class CatalogEnsureDefaultsCommand extends Command
{
    protected $signature = 'catalog:ensure-defaults';

    protected $description = 'Crea el catálogo por defecto en las tiendas que no lo tengan';

    public function handle(EnsureDefaultCatalogAction $action): int
    {
        $created = 0;
        foreach (Company::query()->cursor() as $company) {
            if ($action->execute($company)->wasRecentlyCreated) {
                $created++;
            }
        }
        $this->info("Catálogos creados: {$created}");

        return self::SUCCESS;
    }
}
