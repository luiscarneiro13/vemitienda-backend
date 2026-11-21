<?php

namespace App\Core\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Base de todos los modelos del Core. No aplica ningún scope ni
 * comportamiento de tenancy — es el punto de partida neutro para modelos
 * que no pertenecen a una empresa (p. ej. la propia Company, o catálogos
 * globales como Module).
 */
abstract class CoreModel extends Model
{
    //
}
