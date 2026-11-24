<?php

namespace App\Filament\Resources\Modules\Schemas;

use Filament\Schemas\Schema;

class ModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        // Solo lectura: sin páginas de creación/edición (ver ModuleResource).
        return $schema->components([]);
    }
}
