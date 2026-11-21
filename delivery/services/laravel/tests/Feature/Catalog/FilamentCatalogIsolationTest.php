<?php

namespace Tests\Feature\Catalog;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Filament\Resources\CatalogCategories\Pages\EditCatalogCategory;
use App\Filament\Resources\CatalogCategories\Pages\ListCatalogCategories;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentCatalogIsolationTest extends TestCase
{
    use RefreshDatabase;

    private const STORE_OWNER_PERMISSIONS = [
        'ViewAny:Company', 'View:Company',
        'ViewAny:Branch', 'View:Branch', 'Update:Branch',
        'ViewAny:Catalog', 'View:Catalog', 'Update:Catalog',
        'ViewAny:CatalogCategory', 'View:CatalogCategory', 'Create:CatalogCategory', 'Update:CatalogCategory', 'Delete:CatalogCategory', 'DeleteAny:CatalogCategory', 'Reorder:CatalogCategory',
        'ViewAny:Product', 'View:Product', 'Create:Product', 'Update:Product', 'Delete:Product', 'DeleteAny:Product', 'Reorder:Product',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = array_merge(self::STORE_OWNER_PERMISSIONS, [
            'Create:Company', 'Create:Branch', 'Delete:Branch',
            'Create:Catalog', 'Delete:Catalog',
            'ViewAny:User', 'Create:User',
        ]);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::findOrCreate('super_admin', 'web')->syncPermissions(Permission::all());
        Role::findOrCreate('store_owner', 'web')->syncPermissions(self::STORE_OWNER_PERMISSIONS);
    }

    private function makeStoreOwner(Company $company): User
    {
        $user = User::factory()->create([
            'company_id' => $company->id,
            'current_company_id' => $company->id,
        ]);
        $user->companies()->attach($company->id, ['role' => 'owner']);
        $user->assignRole('store_owner');

        return $user;
    }

    private function makeProductFor(Company $company, string $name = 'Producto'): Product
    {
        $catalog = $company->defaultCatalog()->withoutTenancy()->first();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);

        return Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
            'name' => $name,
        ]);
    }

    public function test_store_owner_lists_only_own_products(): void
    {
        $own = Company::factory()->create();
        $foreign = Company::factory()->create();
        $ownProduct = $this->makeProductFor($own, 'Propio');
        $foreignProduct = $this->makeProductFor($foreign, 'Ajeno');

        $owner = $this->makeStoreOwner($own);
        $this->actingAs($owner);

        Livewire::test(ListProducts::class)
            ->assertCanSeeTableRecords([$ownProduct])
            ->assertCanNotSeeTableRecords([$foreignProduct]);
    }

    public function test_store_owner_cannot_open_edit_page_of_foreign_product(): void
    {
        $own = Company::factory()->create();
        $foreign = Company::factory()->create();
        $foreignProduct = $this->makeProductFor($foreign, 'Ajeno');

        $owner = $this->makeStoreOwner($own);
        $this->actingAs($owner);

        $this->get("/admin/products/{$foreignProduct->id}/edit")->assertNotFound();
    }

    public function test_store_owner_creates_product_with_option_groups(): void
    {
        $undoRepeaterFake = Repeater::fake();

        $company = Company::factory()->create();
        $catalog = $company->defaultCatalog()->withoutTenancy()->first();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);

        $owner = $this->makeStoreOwner($company);
        $this->actingAs($owner);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'catalog_id' => $catalog->id,
                'catalog_category_id' => $category->id,
                'name' => 'Hamburguesa Test',
                'price' => 5.50,
                'is_active' => true,
                'is_available' => true,
                'optionGroups' => [[
                    'name' => 'Tamaño',
                    'selection_type' => 'single',
                    'is_required' => true,
                    'options' => [
                        ['name' => 'Normal', 'price' => 0, 'is_active' => true],
                        ['name' => 'Doble', 'price' => 2, 'is_active' => true],
                    ],
                ]],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $product = Product::withoutTenancy()->where('name', 'Hamburguesa Test')->firstOrFail();
        $this->assertSame($company->id, $product->company_id);

        $groups = ProductOptionGroup::withoutTenancy()->where('product_id', $product->id)->get();
        $this->assertCount(1, $groups);
        $this->assertSame(1, $groups->first()->max_selections);
        $this->assertSame($company->id, $groups->first()->company_id);

        $options = ProductOption::withoutTenancy()->where('product_option_group_id', $groups->first()->id)->get();
        $this->assertCount(2, $options);
        $this->assertTrue($options->every(fn (ProductOption $option): bool => $option->company_id === $company->id));
    }

    public function test_super_admin_cannot_access_products_or_categories_in_filament(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        Livewire::test(ListProducts::class)->assertForbidden();
        Livewire::test(ListCatalogCategories::class)->assertForbidden();
    }

    public function test_store_owner_lists_only_own_categories(): void
    {
        $own = Company::factory()->create();
        $foreign = Company::factory()->create();

        $ownCategory = CatalogCategory::factory()->create([
            'catalog_id' => $own->defaultCatalog()->withoutTenancy()->value('id'),
        ]);
        $foreignCategory = CatalogCategory::factory()->create([
            'catalog_id' => $foreign->defaultCatalog()->withoutTenancy()->value('id'),
        ]);

        $owner = $this->makeStoreOwner($own);
        $this->actingAs($owner);

        Livewire::test(ListCatalogCategories::class)
            ->assertCanSeeTableRecords([$ownCategory])
            ->assertCanNotSeeTableRecords([$foreignCategory]);
    }

    public function test_catalog_resource_has_no_create_page(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        $this->get('/admin/catalogs/create')->assertNotFound();
    }

    public function test_category_with_products_has_no_delete_action(): void
    {
        $company = Company::factory()->create();
        $catalog = $company->defaultCatalog()->withoutTenancy()->first();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);
        Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
        ]);

        $owner = $this->makeStoreOwner($company);
        $this->actingAs($owner);

        Livewire::test(EditCatalogCategory::class, ['record' => $category->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);
    }
}
