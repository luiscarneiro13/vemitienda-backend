<?php

namespace App\Filament\Resources\DatabaseNotifications\Schemas;

use Filament\Schemas\Schema;

class DatabaseNotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        // Recurso de solo lectura: no hay páginas de creación/edición.
        return $schema->components([]);
    }
}
