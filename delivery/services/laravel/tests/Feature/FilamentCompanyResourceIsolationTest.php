<?php

namespace Tests\Feature;

use App\Core\Companies\Models\Company;
use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentCompanyResourceIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'ViewAny:Company', 'View:Company', 'Create:Company',
            'Update:Company', 'Delete:Company', 'DeleteAny:Company',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::findOrCreate('super_admin', 'web');

        $storeOwner = Role::findOrCreate('store_owner', 'web');
        $storeOwner->syncPermissions($permissions);

        $manager = Role::findOrCreate('manager', 'web');
        $manager->syncPermissions(['ViewAny:Company', 'View:Company']);

        Role::findOrCreate('user', 'web');
    }

    public function test_super_admin_lists_and_accesses_all_companies_globally(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);

        $this->assertCount(2, CompanyResource::getEloquentQuery()->get());
        $this->assertTrue(Gate::allows('view', $companyA));
        $this->assertTrue(Gate::allows('view', $companyB));
        $this->assertTrue(Gate::allows('update', $companyB));
        $this->assertTrue(Gate::allows('delete', $companyB));

        Livewire::test(ListCompanies::class)
            ->assertCanSeeTableRecords([$companyA, $companyB]);

        $this->get("/admin/companies/{$companyB->id}/edit")->assertOk();
    }

    public function test_store_owner_can_list_and_access_member_companies_including_multi_company_and_not_foreign_ones(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $companyC = Company::factory()->create();

        $storeOwner = User::factory()->create(['current_company_id' => $companyA->id]);
        $storeOwner->assignRole('store_owner');
        $storeOwner->companies()->attach([$companyA->id, $companyC->id]);

        $this->actingAs($storeOwner);

        $visibleIds = CompanyResource::getEloquentQuery()->pluck('id');
        $this->assertTrue($visibleIds->contains($companyA->id));
        $this->assertTrue($visibleIds->contains($companyC->id));
        $this->assertFalse($visibleIds->contains($companyB->id));

        $this->assertTrue(Gate::allows('view', $companyA));
        $this->assertTrue(Gate::allows('view', $companyC));
        $this->assertFalse(Gate::allows('view', $companyB));

        Livewire::test(ListCompanies::class)
            ->assertCanSeeTableRecords([$companyA, $companyC])
            ->assertCanNotSeeTableRecords([$companyB]);

        $this->get("/admin/companies/{$companyA->id}/edit")->assertOk();
        $this->get("/admin/companies/{$companyC->id}/edit")->assertOk();
    }

    public function test_manager_has_the_same_membership_isolation_and_cannot_create_or_edit(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $manager = User::factory()->create(['current_company_id' => $companyA->id]);
        $manager->assignRole('manager');
        $manager->companies()->attach($companyA->id);

        $this->actingAs($manager);

        $visibleIds = CompanyResource::getEloquentQuery()->pluck('id');
        $this->assertEquals([$companyA->id], $visibleIds->all());

        $this->assertTrue(Gate::allows('view', $companyA));
        $this->assertFalse(Gate::allows('view', $companyB));
        $this->assertFalse(Gate::allows('create', Company::class));
        $this->assertFalse(Gate::allows('update', $companyA));
        $this->assertFalse(Gate::allows('delete', $companyA));

        Livewire::test(ListCompanies::class)
            ->assertCanSeeTableRecords([$companyA])
            ->assertCanNotSeeTableRecords([$companyB]);

        $this->get('/admin/companies/create')->assertForbidden();
        $this->get("/admin/companies/{$companyA->id}/edit")->assertForbidden();
        $this->get("/admin/companies/{$companyB->id}/edit")->assertNotFound();
    }

    public function test_user_without_membership_cannot_query_or_view_any_company(): void
    {
        $companyA = Company::factory()->create();

        $orphanOwner = User::factory()->create();
        $orphanOwner->assignRole('store_owner');

        $this->actingAs($orphanOwner);

        $this->assertCount(0, CompanyResource::getEloquentQuery()->get());
        $this->assertFalse(Gate::allows('view', $companyA));
        $this->assertFalse(Gate::allows('update', $companyA));
        $this->assertFalse(Gate::allows('delete', $companyA));

        Livewire::test(ListCompanies::class)
            ->assertCanNotSeeTableRecords([$companyA]);

        $this->get("/admin/companies/{$companyA->id}/edit")->assertNotFound();
    }

    public function test_direct_edit_url_and_write_or_delete_operations_on_foreign_company_are_rejected(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create(['name' => 'Intacta B']);

        $storeOwnerA = User::factory()->create(['current_company_id' => $companyA->id]);
        $storeOwnerA->assignRole('store_owner');
        $storeOwnerA->companies()->attach($companyA->id);

        $this->actingAs($storeOwnerA);

        $this->get("/admin/companies/{$companyB->id}/edit")->assertNotFound();

        $this->assertFalse(Gate::allows('update', $companyB));
        $this->assertFalse(Gate::allows('delete', $companyB));

        try {
            Livewire::test(EditCompany::class, ['record' => $companyB->getRouteKey()]);
            $this->fail('Se esperaba ModelNotFoundException al montar EditCompany con empresa ajena.');
        } catch (ModelNotFoundException) {
            $this->addToAssertionCount(1);
        }

        Livewire::test(EditCompany::class, ['record' => $companyA->getRouteKey()])
            ->fillForm(['name' => 'Empresa A Editada'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Empresa A Editada', $companyA->fresh()->name);
        $this->assertSame('Intacta B', $companyB->fresh()->name);

        Livewire::test(EditCompany::class, ['record' => $companyA->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('companies', ['id' => $companyA->id]);
        $this->assertNotSoftDeleted('companies', ['id' => $companyB->id]);
    }
}
