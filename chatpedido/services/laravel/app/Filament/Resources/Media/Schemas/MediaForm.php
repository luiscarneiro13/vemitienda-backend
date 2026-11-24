<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        // Solo lectura: los archivos se suben vía MediaManager, no aquí.
        return $schema->components([]);
    }
}
