<?php

namespace App\Domain\Catalog\Listeners;

use App\Core\Companies\Events\CompanyCreated;
use App\Domain\Catalog\Actions\EnsureDefaultCatalogAction;

class CreateDefaultCatalogForCompany
{
    public function __construct(private readonly EnsureDefaultCatalogAction $action) {}

    public function handle(CompanyCreated $event): void
    {
        $this->action->execute($event->company);
    }
}
