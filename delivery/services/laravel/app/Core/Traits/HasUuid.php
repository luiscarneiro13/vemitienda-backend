<?php

namespace App\Core\Traits;

use Illuminate\Support\Str;

/**
 * Genera un UUID en la columna `uuid` al crear el registro, si no viene seteado.
 * Requiere que la tabla tenga una columna `uuid` (string, unique).
 */
trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function resolveRouteBindingFieldForUuid(): string
    {
        return 'uuid';
    }
}
