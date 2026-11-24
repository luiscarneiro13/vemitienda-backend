<?php

namespace App\Core\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Autoasigna `deleted_by` con el usuario autenticado justo antes del soft
 * delete. Requiere que el modelo use SoftDeletes y tenga columna
 * `deleted_by` (FK nullable a `users`).
 */
trait HasDeletedBy
{
    public static function bootHasDeletedBy(): void
    {
        static::deleting(function ($model): void {
            if (Auth::check() && method_exists($model, 'trashed')) {
                $model->forceFill(['deleted_by' => Auth::id()])->saveQuietly();
            }
        });
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
