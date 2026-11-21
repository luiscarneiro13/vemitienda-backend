<?php

namespace App\Filament\Resources\Audits\Schemas;

use Filament\Schemas\Schema;

class AuditForm
{
    public static function configure(Schema $schema): Schema
    {
        // El recurso de Auditoría es de solo lectura: no existen páginas
        // de creación/edición, por lo que este formulario nunca se renderiza.
        return $schema->components([]);
    }
}
