<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Provee un scope de consulta y un helper de comparación sobre la columna
 * `status`. No impone un enum concreto: cada modelo define sus propios
 * valores de estado (p. ej. "active", "inactive", "suspended").
 */
trait HasStatus
{
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }
}
