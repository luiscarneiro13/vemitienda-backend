<?php

namespace App\Core\Traits;

use Illuminate\Support\Str;

/**
 * Genera automáticamente `slug` a partir de `name` al crear el registro,
 * si no viene seteado, garantizando unicidad dentro de la tabla del modelo.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model): void {
            if (empty($model->slug) && ! empty($model->name)) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });
    }

    protected static function generateUniqueSlug(string $source): string
    {
        $slug = Str::slug($source);
        $original = $slug;
        $count = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
