<?php

namespace Tests\Feature\Core;

use App\Core\Companies\Models\Company;
use App\Core\Features\FeatureManager;
use App\Core\Modules\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_enable_and_disable_a_module_for_a_company(): void
    {
        $company = Company::factory()->create();
        Module::create(['name' => 'Billing', 'slug' => 'billing']);

        $manager = app(FeatureManager::class);

        $this->assertFalse($manager->enabled($company, 'billing'));

        $manager->enable($company, 'billing');
        $this->assertTrue($manager->enabled($company, 'billing'));
        $this->assertFalse($manager->disabled($company, 'billing'));

        $manager->disable($company, 'billing');
        $this->assertFalse($manager->enabled($company, 'billing'));
        $this->assertTrue($manager->disabled($company, 'billing'));
    }

    public function test_sync_enables_only_the_given_modules(): void
    {
        $company = Company::factory()->create();
        Module::create(['name' => 'Billing', 'slug' => 'billing']);
        Module::create(['name' => 'Inventory', 'slug' => 'inventory']);

        $manager = app(FeatureManager::class);
        $manager->enable($company, 'inventory');

        $manager->sync($company, ['billing']);

        $this->assertTrue($manager->enabled($company, 'billing'));
        $this->assertFalse($manager->enabled($company, 'inventory'));
    }

    public function test_is_installed_checks_the_global_catalog(): void
    {
        Module::create(['name' => 'Billing', 'slug' => 'billing']);

        $manager = app(FeatureManager::class);

        $this->assertTrue($manager->isInstalled('billing'));
        $this->assertFalse($manager->isInstalled('unknown'));
    }
}
