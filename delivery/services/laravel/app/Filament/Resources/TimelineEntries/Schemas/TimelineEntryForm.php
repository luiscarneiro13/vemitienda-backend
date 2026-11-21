<?php

namespace App\Filament\Resources\TimelineEntries\Schemas;

use Filament\Schemas\Schema;

class TimelineEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        // Solo lectura: sin páginas de creación/edición (ver TimelineEntryResource).
        return $schema->components([]);
    }
}
