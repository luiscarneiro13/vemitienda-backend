<?php

namespace Tests\Feature\Core;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Services\CompanySettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingsManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_set_and_get_company_settings(): void
    {
        $company = Company::factory()->create();
        $manager = app(CompanySettingsManager::class);

        $manager->set($company, 'catalog.template_id', 'grid_2col');
        $manager->set($company, 'catalog.show_search', true, 'boolean');
        $manager->set($company, 'catalog.delivery_min', 15.50, 'float');

        $this->assertSame('grid_2col', $manager->get($company, 'catalog.template_id'));
        $this->assertTrue($manager->get($company, 'catalog.show_search'));
        $this->assertSame(15.50, $manager->get($company, 'catalog.delivery_min'));
        $this->assertNull($manager->get($company, 'non_existent'));
        $this->assertSame('default', $manager->get($company, 'non_existent', 'default'));
    }

    public function test_can_retrieve_settings_by_group(): void
    {
        $company = Company::factory()->create();
        $manager = app(CompanySettingsManager::class);

        $manager->set($company, 'catalog.template_id', 'grid_2col', 'string', 'catalog');
        $manager->set($company, 'catalog.primary_color', '#ff6b00', 'string', 'catalog');
        $manager->set($company, 'billing.tax_rate', 16, 'integer', 'billing');

        $catalogGroup = $manager->group($company, 'catalog');
        $this->assertCount(2, $catalogGroup);
        $this->assertSame('grid_2col', $catalogGroup['catalog.template_id']);
        $this->assertSame('#ff6b00', $catalogGroup['catalog.primary_color']);

        $billingGroup = $manager->group($company, 'billing');
        $this->assertCount(1, $billingGroup);
        $this->assertSame(16, $billingGroup['billing.tax_rate']);
    }

    public function test_can_set_many_settings_at_once(): void
    {
        $company = Company::factory()->create();
        $manager = app(CompanySettingsManager::class);

        $manager->setMany($company, [
            'catalog.template_id' => 'dark_midnight',
            'catalog.show_search' => false,
            'catalog.accent_color' => '#121212',
        ], 'catalog');

        $this->assertSame('dark_midnight', $manager->get($company, 'catalog.template_id'));
        $this->assertFalse($manager->get($company, 'catalog.show_search'));
        $this->assertSame('#121212', $manager->get($company, 'catalog.accent_color'));
    }

    public function test_settings_are_isolated_between_companies(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $manager = app(CompanySettingsManager::class);

        $manager->set($companyA, 'catalog.template_id', 'grid_2col');
        $manager->set($companyB, 'catalog.template_id', 'compact_list');

        $this->assertSame('grid_2col', $manager->get($companyA, 'catalog.template_id'));
        $this->assertSame('compact_list', $manager->get($companyB, 'catalog.template_id'));
    }
}
