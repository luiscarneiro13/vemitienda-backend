<?php

namespace App\Core\Settings;

use App\Core\Settings\Models\Setting;
use Illuminate\Support\Collection;

class SettingsRepository
{
    /**
     * @return array<string, array{value: ?string, type: string}>
     */
    public function allAsArray(): array
    {
        return Setting::query()
            ->get(['key', 'value', 'type'])
            ->keyBy('key')
            ->map(fn (Setting $setting) => ['value' => $setting->value, 'type' => $setting->type])
            ->all();
    }

    /**
     * @return Collection<int, Setting>
     */
    public function forGroup(string $group): Collection
    {
        return Setting::query()->where('group', $group)->get();
    }

    public function find(string $key): ?Setting
    {
        return Setting::query()->where('key', $key)->first();
    }

    public function upsert(string $key, string $value, string $type, ?string $group): Setting
    {
        return Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }
}
