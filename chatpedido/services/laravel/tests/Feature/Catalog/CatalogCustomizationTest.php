<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Companies\Services\CompanySettingsManager;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Services\PublicCatalogPresenter;
use App\Filament\Pages\CatalogCustomization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogCustomizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'store_owner']);
        Role::firstOrCreate(['name' => 'user']);
    }

    private function makeStore(): array
    {
        $company = Company::factory()->create([
            'name' => 'Burger Mania',
            'slug' => 'burger-mania',
            'is_active' => true,
            'status' => 'active',
        ]);

        $branch = Branch::factory()->create([
            'company_id' => $company->id,
            'name' => 'Sucursal Principal',
            'whatsapp_number' => '584120000000',
            'status' => 'active',
        ]);

        $catalog = $company->defaultCatalog()->withoutTenancy()->first()
            ?? Catalog::factory()->create(['company_id' => $company->id, 'is_default' => true, 'is_active' => true]);

        $category = CatalogCategory::factory()->create([
            'catalog_id' => $catalog->id,
            'name' => 'Hamburguesas',
            'icon' => '🍔',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
            'name' => 'Doble Cheddar Smash',
            'price' => 10.00,
            'compare_at_price' => 13.50,
            'badge' => 'Top Ventas 🔥',
            'is_featured' => true,
            'is_active' => true,
            'is_available' => true,
        ]);

        return [$company, $branch, $catalog, $category, $product];
    }

    public function test_public_catalog_payload_includes_template_settings_and_extended_fields(): void
    {
        [$company, $branch, $catalog, $category, $product] = $this->makeStore();

        $settingsManager = app(CompanySettingsManager::class);
        $settingsManager->setMany($company, [
            'catalog.template_id' => 'compact_list',
            'catalog.primary_color' => '#ff6b00',
            'catalog.secondary_color' => '#b7131a',
            'catalog.show_promo_banner' => true,
            'catalog.promo_title' => '¡2x1 los jueves!',
            'catalog.promo_code' => 'JUEVES2X1',
            'catalog.show_free_shipping_bar' => true,
            'catalog.free_shipping_threshold' => 25.00,
        ], 'catalog');

        $presenter = app(PublicCatalogPresenter::class);
        $payload = $presenter->present($company, $branch, $catalog);

        // Validar Settings
        $this->assertSame('compact_list', $payload['settings']['template_id']);
        $this->assertSame('#ff6b00', $payload['settings']['primary_color']);
        $this->assertSame('#b7131a', $payload['settings']['secondary_color']);
        $this->assertTrue($payload['settings']['show_promo_banner']);
        $this->assertSame('¡2x1 los jueves!', $payload['settings']['promo_title']);
        $this->assertSame('JUEVES2X1', $payload['settings']['promo_code']);
        $this->assertTrue($payload['settings']['show_free_shipping_bar']);
        $this->assertSame(25.00, $payload['settings']['free_shipping_threshold']);

        // Validar Categoría con ícono
        $this->assertSame('🍔', $payload['categories'][0]['icon']);

        // Validar Producto con campos extendidos
        $productPayload = $payload['categories'][0]['products'][0];
        $this->assertSame(1000, $productPayload['price_cents']);
        $this->assertSame(1350, $productPayload['compare_at_price_cents']);
        $this->assertSame('Top Ventas 🔥', $productPayload['badge']);
        $this->assertTrue($productPayload['is_featured']);

        // Validar Carrusel de Destacados
        $this->assertCount(1, $payload['featured_products']);
        $this->assertSame($product->id, $payload['featured_products'][0]['id']);
    }

    public function test_public_catalog_view_renders_selected_template(): void
    {
        [$company, $branch] = $this->makeStore();

        $settingsManager = app(CompanySettingsManager::class);
        $settingsManager->set($company, 'catalog.template_id', 'compact_list', 'string', 'catalog');

        $response = $this->get(route('public.catalog.show', [
            'companySlug' => $company->slug,
            'branchUuid' => $branch->uuid,
        ]));

        $response->assertOk();
        $response->assertSee('plist-compact');
        $response->assertSee('pcard-compact');
        $response->assertSee('Doble Cheddar Smash');
    }

    public function test_public_catalog_renders_dark_mode_class_when_midnight_template_selected(): void
    {
        [$company, $branch] = $this->makeStore();

        $settingsManager = app(CompanySettingsManager::class);
        $settingsManager->set($company, 'catalog.template_id', 'dark_midnight', 'string', 'catalog');

        $response = $this->get(route('public.catalog.show', [
            'companySlug' => $company->slug,
            'branchUuid' => $branch->uuid,
        ]));

        $response->assertOk();
        $response->assertSee('dark-mode');
    }

    public function test_store_owner_can_access_catalog_customization_page(): void
    {
        [$company] = $this->makeStore();

        $owner = User::factory()->create();
        $owner->assignRole('store_owner');
        $owner->companies()->attach($company->id, ['role' => 'owner']);

        $this->actingAs($owner);

        Livewire::test(CatalogCustomization::class)
            ->assertSuccessful()
            ->assertSee('Plantilla Visual del Menú')
            ->assertSee('10 Diseños Disponibles')
            ->assertSee('Ofertas Flash & Urgencia')
            ->assertSee('Wizard Paso a Paso (Arma tu Combo)')
            ->assertSee('Combos Familiares & Catering')
            ->assertSee('Directorio Multi-Marca / Cocinas')
            ->assertSee('Menú Fitness & Saludable')
            ->assertSee('templates-demo/grid_2col.html')
            ->assertSee('Ver demo')
            ->assertSee('Paleta de Colores de la Marca');
    }

    public function test_store_owner_can_save_customization_settings(): void
    {
        [$company] = $this->makeStore();

        $owner = User::factory()->create();
        $owner->assignRole('store_owner');
        $owner->companies()->attach($company->id, ['role' => 'owner']);

        $this->actingAs($owner);

        Livewire::test(CatalogCustomization::class)
            ->set('data.template_id', 'editorial_cards')
            ->set('data.primary_color', '#123456')
            ->set('data.secondary_color', '#654321')
            ->set('data.show_promo_banner', true)
            ->set('data.promo_title', 'Super Promo')
            ->call('save')
            ->assertHasNoErrors();

        $manager = app(CompanySettingsManager::class);
        $this->assertSame('editorial_cards', $manager->get($company, 'catalog.template_id'));
        $this->assertSame('#123456', $manager->get($company, 'catalog.primary_color'));
        $this->assertSame('#654321', $manager->get($company, 'catalog.secondary_color'));
        $this->assertTrue($manager->get($company, 'catalog.show_promo_banner'));
        $this->assertSame('Super Promo', $manager->get($company, 'catalog.promo_title'));
    }

    public function test_unauthorized_user_cannot_access_catalog_customization(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $this->assertFalse(CatalogCustomization::canAccess());
    }
}
