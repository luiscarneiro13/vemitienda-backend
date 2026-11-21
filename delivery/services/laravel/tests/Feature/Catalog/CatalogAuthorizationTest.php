<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private const STORE_OWNER_PERMISSIONS = [
        'ViewAny:Company', 'View:Company', 'Update:Company',
        'ViewAny:Branch', 'View:Branch', 'Create:Branch', 'Update:Branch', 'Delete:Branch', 'DeleteAny:Branch',
        'ViewAny:User', 'View:User', 'Create:User', 'Update:User', 'Delete:User', 'DeleteAny:User',
        'ViewAny:Catalog', 'View:Catalog', 'Update:Catalog',
        'ViewAny:CatalogCategory', 'View:CatalogCategory', 'Create:CatalogCategory', 'Update:CatalogCategory', 'Delete:CatalogCategory', 'DeleteAny:CatalogCategory', 'Reorder:CatalogCategory',
        'ViewAny:Product', 'View:Product', 'Create:Product', 'Update:Product', 'Delete:Product', 'DeleteAny:Product', 'Reorder:Product',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = array_merge(self::STORE_OWNER_PERMISSIONS, [
            'Create:Company', 'Delete:Company',
            'Create:Catalog', 'Delete:Catalog',
            'Delete:User', 'DeleteAny:User',
        ]);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::findOrCreate('super_admin', 'web')->syncPermissions(Permission::all());
        Role::findOrCreate('store_owner', 'web')->syncPermissions(self::STORE_OWNER_PERMISSIONS);
        Role::findOrCreate('store_admin', 'web')->syncPermissions(self::STORE_OWNER_PERMISSIONS);
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

    private function makeProduct(Company $company): Product
    {
        $catalog = $company->defaultCatalog()->withoutTenancy()->first()
            ?? Catalog::factory()->create(['company_id' => $company->id]);
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);

        return Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
        ]);
    }

    public function test_store_owner_sees_only_own_company_and_cannot_create(): void
    {
        $own = Company::factory()->create();
        $foreign = Company::factory()->create();
        $owner = $this->makeStoreOwner($own);

        $this->actingAs($owner);

        $visibleIds = CompanyResource::getEloquentQuery()->pluck('id');
        $this->assertTrue($visibleIds->contains($own->id));
        $this->assertFalse($visibleIds->contains($foreign->id));
        $this->assertFalse(Gate::allows('create', Company::class));
        $this->assertFalse(Gate::allows('delete', $own));
        $this->assertTrue(Gate::allows('update', $own));
    }

    public function test_store_owner_can_view_and_update_own_product(): void
    {
        $company = Company::factory()->create();
        $product = $this->makeProduct($company);
        $owner = $this->makeStoreOwner($company);

        $this->actingAs($owner);

        $this->assertTrue(Gate::allows('view', $product));
        $this->assertTrue(Gate::allows('update', $product));
        $this->assertTrue(Gate::allows('viewAny', Product::class));
    }

    public function test_store_owner_cannot_view_or_update_product_of_other_store(): void
    {
        $own = Company::factory()->create();
        $foreign = Company::factory()->create();
        $foreignProduct = $this->makeProduct($foreign);
        $owner = $this->makeStoreOwner($own);

        $this->actingAs($owner);

        $this->assertFalse(Gate::allows('view', $foreignProduct));
        $this->assertFalse(Gate::allows('update', $foreignProduct));
        $this->assertFalse(Gate::allows('delete', $foreignProduct));
    }

    public function test_store_owner_cannot_create_or_delete_catalog(): void
    {
        $company = Company::factory()->create();
        $catalog = $company->defaultCatalog()->withoutTenancy()->first();
        $owner = $this->makeStoreOwner($company);

        $this->actingAs($owner);

        $this->assertFalse(Gate::allows('create', Catalog::class));
        $this->assertFalse(Gate::allows('delete', $catalog));
        $this->assertFalse(Gate::allows('deleteAny', Catalog::class));
        $this->assertTrue(Gate::allows('view', $catalog));
        $this->assertTrue(Gate::allows('update', $catalog));
    }

    public function test_store_owner_can_manage_own_branches_but_not_foreign_ones(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);
        $owner = $this->makeStoreOwner($company);

        $this->actingAs($owner);

        $this->assertTrue(Gate::allows('create', Branch::class));
        $this->assertTrue(Gate::allows('delete', $branch));
        $this->assertTrue(Gate::allows('deleteAny', Branch::class));
        $this->assertTrue(Gate::allows('view', $branch));
        $this->assertTrue(Gate::allows('update', $branch));
    }

    public function test_store_owner_cannot_manage_branch_of_other_store(): void
    {
        $own = Company::factory()->create();
        $foreign = Company::factory()->create();
        $foreignBranch = Branch::factory()->create(['company_id' => $foreign->id]);
        $owner = $this->makeStoreOwner($own);

        $this->actingAs($owner);

        $this->assertFalse(Gate::allows('view', $foreignBranch));
        $this->assertFalse(Gate::allows('update', $foreignBranch));
        $this->assertFalse(Gate::allows('delete', $foreignBranch));
    }

    public function test_store_owner_can_manage_users_of_own_company_only(): void
    {
        $own = Company::factory()->create();
        $foreign = Company::factory()->create();

        $foreignUser = User::factory()->create(['company_id' => $foreign->id]);
        $foreignUser->companies()->attach($foreign->id, ['role' => 'owner']);

        $owner = $this->makeStoreOwner($own);

        $storeAdmin = User::factory()->create([
            'company_id' => $own->id,
            'current_company_id' => $own->id,
        ]);
        $storeAdmin->companies()->attach($own->id, ['role' => 'admin']);
        $storeAdmin->assignRole('store_admin');

        $this->actingAs($owner);

        $this->assertTrue(Gate::allows('viewAny', User::class));
        $this->assertTrue(Gate::allows('create', User::class));
        $this->assertTrue(Gate::allows('view', $owner));
        // El dueño de la tienda no puede editarse a sí mismo
        $this->assertFalse(Gate::allows('update', $owner));
        // Ningún usuario puede eliminarse a sí mismo
        $this->assertFalse(Gate::allows('delete', $owner));

        // Puede ver, editar y borrar a otro administrador/usuario de su propia empresa
        $this->assertTrue(Gate::allows('view', $storeAdmin));
        $this->assertTrue(Gate::allows('update', $storeAdmin));
        $this->assertTrue(Gate::allows('delete', $storeAdmin));

        // No puede gestionar usuarios de otra empresa
        $this->assertFalse(Gate::allows('view', $foreignUser));
        $this->assertFalse(Gate::allows('update', $foreignUser));
        $this->assertFalse(Gate::allows('delete', $foreignUser));
        $this->assertTrue(Gate::allows('deleteAny', User::class));
    }

    public function test_super_admin_can_do_everything(): void
    {
        $company = Company::factory()->create();
        $catalog = $company->defaultCatalog()->withoutTenancy()->first();
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id]);
        $product = $this->makeProduct($company);
        $branch = Branch::factory()->create(['company_id' => $company->id]);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        $this->assertTrue(Gate::allows('create', Catalog::class));
        $this->assertTrue(Gate::allows('delete', $catalog));
        $this->assertTrue(Gate::allows('create', Branch::class));
        $this->assertTrue(Gate::allows('delete', $branch));

        // El superadmin no puede ver ni gestionar productos o categorías de la tienda
        $this->assertFalse(Gate::allows('viewAny', Product::class));
        $this->assertFalse(Gate::allows('view', $product));
        $this->assertFalse(Gate::allows('update', $product));
        $this->assertFalse(Gate::allows('delete', $product));

        $this->assertFalse(Gate::allows('viewAny', CatalogCategory::class));
        $this->assertFalse(Gate::allows('view', $category));
        $this->assertFalse(Gate::allows('update', $category));
        $this->assertFalse(Gate::allows('delete', $category));
    }

    public function test_user_created_from_filament_with_company_gets_membership(): void
    {
        $company = Company::factory()->create();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Empleado Nuevo',
                'email' => 'empleado@example.com',
                'password' => 'password',
                'company_id' => $company->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'empleado@example.com')->firstOrFail();

        $this->assertTrue($user->companies()->whereKey($company->id)->exists());
        $this->assertSame($company->id, $user->current_company_id);
    }
}
