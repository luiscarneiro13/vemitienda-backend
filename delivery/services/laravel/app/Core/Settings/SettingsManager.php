<?php

namespace App\Core\Settings;

use Illuminate\Support\Facades\Validator;

/**
 * Motor de configuración: no es solo una tabla clave/valor. Soporta
 * grupos, tipado (con casting automático al leer/escribir), validación
 * opcional antes de persistir, y cachea el conjunto completo en memoria.
 */
class SettingsManager
{
    public function __construct(
        private readonly SettingsRepository $repository,
        private readonly SettingsCache $cache,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $raw = $this->cache->remember(fn () => $this->repository->allAsArray());

        return collect($raw)->map(fn ($row) => $this->cast($row['value'], $row['type']))->all();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function group(string $group): array
    {
        return $this->repository->forGroup($group)
            ->mapWithKeys(fn ($setting) => [$setting->key => $this->cast($setting->value, $setting->type)])
            ->all();
    }

    /**
     * @param  'string'|'integer'|'float'|'boolean'|'array'  $type
     * @param  array<int, mixed>  $rules  Reglas de validación de Laravel, opcionales.
     */
    public function set(string $key, mixed $value, string $type = 'string', ?string $group = null, array $rules = []): void
    {
        if ($rules) {
            Validator::make([$key => $value], [$key => $rules])->validate();
        }

        $this->repository->upsert($key, $this->serialize($value, $type), $type, $group);

        $this->cache->forget();
    }

    private function cast(?string $raw, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $raw,
            'float' => (float) $raw,
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'array' => json_decode($raw ?? '[]', true),
            default => $raw,
        };
    }

    private function serialize(mixed $value, string $type): string
    {
        return match ($type) {
            'array' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }
}
