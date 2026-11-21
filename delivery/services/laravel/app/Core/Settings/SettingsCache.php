<?php

namespace App\Core\Settings;

use Closure;
use Illuminate\Support\Facades\Cache;

class SettingsCache
{
    private const KEY = 'core.settings.all';

    public function remember(Closure $callback): array
    {
        return Cache::rememberForever(self::KEY, $callback);
    }

    public function forget(): void
    {
        Cache::forget(self::KEY);
    }
}
