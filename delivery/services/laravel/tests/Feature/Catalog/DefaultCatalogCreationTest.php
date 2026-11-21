<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Actions\EnsureDefaultCatalogAction;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Services\CatalogResolver;
use App\Filament\Resources\Companies\Pages\CreateCompany;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DefaultCatalogCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
    }

    public function test_creating_company_creates_one_default_catalog(): void
    {
        $company = Company::factory()->create();

        $catalogs = Catalog::withoutTenancy()
            ->where('company_id', $company->id)
            ->where('is_default', true)
            ->get();

        $this->assertCount(1, $catalogs);
        $this->assertTrue($catalogs->first()->is_active);
        $this->assertSame('Catálogo de '.$company->name, $catalogs->first()->name);
    }

    public function test_action_is_idempotent(): void
    {
        $company = Company::factory()->create();
        $action = app(EnsureDefaultCatalogAction::class);

        $first = $action->execute($company);
        $second = $action->execute($company);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(
            1,
            Catalog::withoutTenancy()->where('company_id', $company->id)->where('is_default', true)->count()
        );
    }

    public function test_command_creates_missing_default_catalogs(): void
    {
        $company = Company::factory()->create();
        $company->defaultCatalog()->withoutTenancy()->delete();

        $this->assertNull($company->defaultCatalog()->withoutTenancy()->first());

        $this->artisan('catalog:ensure-defaults')->assertSuccessful();

        $this->assertNotNull($company->defaultCatalog()->withoutTenancy()->first());
    }

    public function test_creating_company_from_filament_creates_default_catalog(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        Livewire::test(CreateCompany::class)
            ->fillForm(['name' => 'Tienda Test', 'slug' => 'tienda-test'])
            ->call('create')
            ->assertHasNoFormErrors();

        $company = Company::where('slug', 'tienda-test')->firstOrFail();

        $this->assertSame(
            1,
            Catalog::withoutTenancy()->where('company_id', $company->id)->where('is_default', true)->count()
        );
    }

    public function test_resolver_returns_default_active_catalog_of_branch_company(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);

        $catalog = app(CatalogResolver::class)->forBranch($branch);

        $this->assertNotNull($catalog);
        $this->assertTrue($catalog->is_default);
        $this->assertSame($company->id, $catalog->company_id);
    }

    public function test_resolver_returns_null_when_catalog_inactive(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);

        $company->defaultCatalog()->withoutTenancy()->update(['is_active' => false]);

        $this->assertNull(app(CatalogResolver::class)->forBranch($branch));
    }
}
