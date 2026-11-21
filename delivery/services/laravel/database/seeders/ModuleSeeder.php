<?php

namespace Database\Seeders;

use App\Core\Modules\ModuleRegistry;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Sincroniza el catálogo global con lo detectado en Modules/. En una
     * plantilla nueva no hay módulos de negocio aún, así que esto no crea
     * nada — deja el mecanismo listo para cuando se añada el primero.
     */
    public function run(): void
    {
        app(ModuleRegistry::class)->sync();
    }
}
