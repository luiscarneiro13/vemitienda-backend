<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Services\CategoryIconSuggester;
use App\Filament\Resources\CatalogCategories\Pages\CreateCatalogCategory;
use App\Filament\Resources\CatalogCategories\Pages\EditCatalogCategory;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryIconSuggesterTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Catalog $catalog;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--ignore-existing-policies' => true,
            '--option' => 'permissions',
            '--no-interaction' => true,
        ]);

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $company = Company::factory()->create();

        $this->owner = User::factory()->create([
            'company_id' => $company->id,
            'current_company_id' => $company->id,
        ]);
        $this->owner->companies()->attach($company->id, ['role' => 'owner']);
        $this->owner->assignRole('store_owner');

        $this->catalog = Catalog::factory()->create([
            'company_id' => $company->id,
            'is_default' => true,
        ]);
    }

    public function test_suggests_burger_icons_for_burger_names(): void
    {
        $suggestions = CategoryIconSuggester::suggest('Hamburguesas Especiales');

        $this->assertContains('🍔', $suggestions);
        $this->assertNotEmpty($suggestions);
    }

    public function test_suggests_pizza_icons_for_pizza_names(): void
    {
        $suggestions = CategoryIconSuggester::suggest('Pizzas Familiares');

        $this->assertContains('🍕', $suggestions);
        $this->assertNotEmpty($suggestions);
    }

    public function test_suggests_drink_icons_for_beverage_names(): void
    {
        $suggestions = CategoryIconSuggester::suggest('Bebidas y Refrescos');

        $this->assertContains('🥤', $suggestions);
        $this->assertNotEmpty($suggestions);
    }

    public function test_suggests_dessert_icons_for_dessert_names(): void
    {
        $suggestions = CategoryIconSuggester::suggest('Postres y Helados');

        $this->assertContains('🍰', $suggestions);
        $this->assertContains('🍦', $suggestions);
    }

    public function test_suggests_defaults_when_name_is_generic(): void
    {
        $suggestions = CategoryIconSuggester::suggest('Sección Especial 123');

        $this->assertNotEmpty($suggestions);
        $this->assertContains('⭐', $suggestions);
    }

    public function test_suggests_pharmacy_icons_for_health_names(): void
    {
        $suggestions = CategoryIconSuggester::suggest('Medicamentos y Farmacia');

        $this->assertContains('💊', $suggestions);
        $this->assertNotEmpty($suggestions);
    }

    public function test_suggests_hardware_icons_for_hardware_names(): void
    {
        $suggestions = CategoryIconSuggester::suggest('Herramientas y Ferretería');

        $this->assertContains('🔧', $suggestions);
        $this->assertNotEmpty($suggestions);
    }

    public function test_grouped_icons_has_all_expected_food_sections(): void
    {
        $grouped = CategoryIconSuggester::groupedIcons();

        $this->assertArrayHasKey('Comida Rápida', $grouped);
        $this->assertArrayHasKey('Pizzas & Pastas', $grouped);
        $this->assertArrayHasKey('Bebidas & Cafés', $grouped);
        $this->assertArrayHasKey('Farmacia & Salud', $grouped);
        $this->assertArrayHasKey('Ferretería & Hogar', $grouped);
        $this->assertArrayHasKey('Postres & Dulces', $grouped);
        $this->assertArrayHasKey('Promos & Especiales', $grouped);
    }

    public function test_can_create_category_with_selected_icon(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($this->owner);

        Livewire::test(CreateCatalogCategory::class)
            ->assertSuccessful()
            ->fillForm([
                'catalog_id' => $this->catalog->id,
                'name' => 'Hamburguesas Smash',
                'icon' => '🍔',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $category = CatalogCategory::query()->where('name', 'Hamburguesas Smash')->first();
        $this->assertNotNull($category);
        $this->assertSame('🍔', $category->icon);
    }

    public function test_cannot_create_category_without_selecting_catalog(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($this->owner);

        Livewire::test(CreateCatalogCategory::class)
            ->assertSuccessful()
            ->fillForm([
                'name' => 'Categoría Sin Catálogo',
                'icon' => '🍕',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['catalog_id' => 'required']);
    }

    public function test_can_edit_category_icon(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($this->owner);

        $category = CatalogCategory::factory()->create([
            'catalog_id' => $this->catalog->id,
            'company_id' => $this->owner->company_id,
            'name' => 'Bebidas',
            'icon' => '🥤',
        ]);

        Livewire::test(EditCatalogCategory::class, ['record' => $category->getRouteKey()])
            ->assertSuccessful()
            ->fillForm([
                'icon' => '🧃',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $category->refresh();
        $this->assertSame('🧃', $category->icon);
    }
}
