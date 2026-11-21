<?php

namespace App\Core\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Autoasigna `updated_by` con el usuario autenticado en cada actualización.
 * Requiere una columna `updated_by` (FK nullable a `users`).
 */
trait HasUpdatedBy
{
    public static function bootHasUpdatedBy(): void
    {
        static::updating(function ($model): void {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
