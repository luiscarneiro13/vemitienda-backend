<?php

namespace App\Filament\Resources\Catalogs\Pages;

use App\Filament\Resources\Catalogs\CatalogResource;
use Filament\Resources\Pages\EditRecord;

class EditCatalog extends EditRecord
{
    protected static string $resource = CatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
