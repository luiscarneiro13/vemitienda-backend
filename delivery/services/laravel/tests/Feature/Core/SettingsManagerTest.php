<?php

namespace Tests\Feature\Core;

use App\Core\Settings\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SettingsManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_set_and_get_a_string_setting(): void
    {
        $settings = app(SettingsManager::class);

        $settings->set('app_name', 'Acme', 'string', 'general');

        $this->assertSame('Acme', $settings->get('app_name'));
    }

    public function test_casts_values_according_to_their_declared_type(): void
    {
        $settings = app(SettingsManager::class);

        $settings->set('max_users', 25, 'integer');
        $settings->set('maintenance_mode', true, 'boolean');
        $settings->set('allowed_ips', ['127.0.0.1', '10.0.0.1'], 'array');

        $this->assertSame(25, $settings->get('max_users'));
        $this->assertTrue($settings->get('maintenance_mode'));
        $this->assertSame(['127.0.0.1', '10.0.0.1'], $settings->get('allowed_ips'));
    }

    public function test_get_returns_default_when_key_does_not_exist(): void
    {
        $settings = app(SettingsManager::class);

        $this->assertSame('fallback', $settings->get('missing_key', 'fallback'));
    }

    public function test_group_returns_only_settings_of_that_group(): void
    {
        $settings = app(SettingsManager::class);

        $settings->set('app_name', 'Acme', 'string', 'general');
        $settings->set('support_email', 'a@b.com', 'string', 'general');
        $settings->set('driver', 'local', 'string', 'media');

        $general = $settings->group('general');

        $this->assertSame(['app_name' => 'Acme', 'support_email' => 'a@b.com'], $general);
    }

    public function test_set_validates_the_value_when_rules_are_given(): void
    {
        $settings = app(SettingsManager::class);

        $this->expectException(ValidationException::class);

        $settings->set('support_email', 'not-an-email', 'string', 'general', ['email']);
    }

    public function test_cache_is_invalidated_after_set(): void
    {
        $settings = app(SettingsManager::class);

        $settings->set('app_name', 'First', 'string');
        $this->assertSame('First', $settings->get('app_name'));

        $settings->set('app_name', 'Second', 'string');
        $this->assertSame('Second', $settings->get('app_name'));
    }
}
