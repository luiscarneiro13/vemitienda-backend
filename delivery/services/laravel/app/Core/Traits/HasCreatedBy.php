<?php

namespace App\Core\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Autoasigna `created_by` con el usuario autenticado al crear el registro.
 * Requiere una columna `created_by` (FK nullable a `users`).
 */
trait HasCreatedBy
{
    public static function bootHasCreatedBy(): void
    {
        static::creating(function ($model): void {
            if (! $model->created_by && Auth::check()) {
                $model->created_by = Auth::id();
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
