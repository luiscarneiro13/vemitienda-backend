<?php

namespace Database\Seeders;

use App\Core\Settings\SettingsManager;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SettingsManager::class);

        $settings->set('app_name', 'Laravel Starter', 'string', 'general');
        $settings->set('support_email', 'soporte@example.com', 'string', 'general');
        $settings->set('timezone', config('app.timezone'), 'string', 'general');
    }
}
