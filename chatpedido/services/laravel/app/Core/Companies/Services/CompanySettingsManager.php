<?php

namespace App\Core\Companies\Services;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Models\CompanySetting;
use App\Domain\Catalog\Services\PublicCatalogCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CompanySettingsManager
{
    private const CACHE_PREFIX = 'company_settings:';

    public function all(Company|int $company): array
    {
        $companyId = $company instanceof Company ? $company->id : $company;

        return Cache::rememberForever(self::CACHE_PREFIX.$companyId, function () use ($companyId) {
            return CompanySetting::withoutTenancy()
                ->where('company_id', $companyId)
                ->get(['key', 'value', 'type', 'group'])
                ->keyBy('key')
                ->map(fn (CompanySetting $setting) => [
                    'value' => $this->cast($setting->value, $setting->type),
                    'type' => $setting->type,
                    'group' => $setting->group,
                ])
                ->all();
        });
    }

    public function get(Company|int $company, string $key, mixed $default = null): mixed
    {
        $all = $this->all($company);

        return isset($all[$key]) ? $all[$key]['value'] : $default;
    }

    public function group(Company|int $company, string $group): array
    {
        $all = $this->all($company);
        $result = [];

        foreach ($all as $key => $item) {
            if ($item['group'] === $group) {
                $result[$key] = $item['value'];
            }
        }

        return $result;
    }

    public function set(
        Company|int $company,
        string $key,
        mixed $value,
        string $type = 'string',
        string $group = 'catalog',
        array $rules = []
    ): void {
        $companyId = $company instanceof Company ? $company->id : $company;

        if ($rules !== []) {
            Validator::make([$key => $value], [$key => $rules])->validate();
        }

        CompanySetting::withoutTenancy()->updateOrCreate(
            ['company_id' => $companyId, 'key' => $key],
            [
                'value' => $this->serialize($value, $type),
                'type' => $type,
                'group' => $group,
            ]
        );

        $this->forget($companyId);
    }

    /**
     * @param  array<string, mixed>  $settings  clave => valor
     * @param  array<string, string>  $types  clave => tipo (opcional, fallback 'string')
     */
    public function setMany(Company|int $company, array $settings, string $group = 'catalog', array $types = []): void
    {
        $companyId = $company instanceof Company ? $company->id : $company;
        $now = now();

        foreach ($settings as $key => $value) {
            $type = $types[$key] ?? $this->detectType($value);

            CompanySetting::withoutTenancy()->updateOrCreate(
                ['company_id' => $companyId, 'key' => $key],
                [
                    'value' => $this->serialize($value, $type),
                    'type' => $type,
                    'group' => $group,
                    'updated_at' => $now,
                ]
            );
        }

        $this->forget($companyId);
    }

    public function forget(Company|int $company): void
    {
        $companyId = $company instanceof Company ? $company->id : $company;
        Cache::forget(self::CACHE_PREFIX.$companyId);
        PublicCatalogCache::bumpCompany((int) $companyId);
    }

    private function cast(?string $raw, string $type): mixed
    {
        return match ($type) {
            'integer', 'int' => $raw !== null ? (int) $raw : null,
            'float', 'decimal' => $raw !== null ? (float) $raw : null,
            'boolean', 'bool' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'array', 'json' => json_decode($raw ?? '[]', true),
            default => $raw,
        };
    }

    private function serialize(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'array', 'json' => json_encode($value),
            'boolean', 'bool' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    private function detectType(mixed $value): string
    {
        return match (gettype($value)) {
            'boolean' => 'boolean',
            'integer' => 'integer',
            'double' => 'float',
            'array' => 'array',
            default => 'string',
        };
    }
}
