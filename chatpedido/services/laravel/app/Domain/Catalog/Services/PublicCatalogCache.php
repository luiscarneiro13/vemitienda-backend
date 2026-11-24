<?php

namespace App\Domain\Catalog\Services;

use Illuminate\Support\Facades\Cache;

class PublicCatalogCache
{
    public const DIRECTORY_VERSION_KEY = 'public_directory:version';

    public const COMPANY_VERSION_PREFIX = 'public_catalog:company_version:';

    public static function directoryVersion(): int
    {
        return (int) Cache::rememberForever(self::DIRECTORY_VERSION_KEY, fn (): int => 1);
    }

    public static function companyVersion(int $companyId): int
    {
        return (int) Cache::rememberForever(self::COMPANY_VERSION_PREFIX.$companyId, fn (): int => 1);
    }

    public static function bumpDirectory(): void
    {
        $current = (int) Cache::get(self::DIRECTORY_VERSION_KEY, 1);
        Cache::forever(self::DIRECTORY_VERSION_KEY, $current + 1);
    }

    public static function bumpCompany(?int $companyId, bool $alsoDirectory = false): void
    {
        if ($companyId) {
            $key = self::COMPANY_VERSION_PREFIX.$companyId;
            $current = (int) Cache::get($key, 1);
            Cache::forever($key, $current + 1);
        }

        if ($alsoDirectory) {
            self::bumpDirectory();
        }
    }
}
