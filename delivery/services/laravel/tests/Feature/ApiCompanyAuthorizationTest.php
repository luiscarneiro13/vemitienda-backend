<?php

namespace Tests\Feature;

use App\Core\Companies\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiCompanyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['companies.view', 'companies.create', 'companies.update', 'companies.delete'] as $perm) {
            Permission::findOrCreate($perm, 'web');
        }
    }

    public function test_authenticated_user_without_permission_or_membership_cannot_list_foreign_companies(): void
    {
        Company::factory()->count(2)->create();

        $plainUser = User::factory()->create();
        Sanctum::actingAs($plainUser);
        $this->getJson('/api/v1/companies')->assertForbidden();

        $viewerWithoutMembership = User::factory()->create();
        $viewerWithoutMembership->givePermissionTo('companies.view');
        Sanctum::actingAs($viewerWithoutMembership);

        $this->getJson('/api/v1/companies')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_member_of_company_a_can_access_a_but_cannot_view_company_b(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $memberA = User::factory()->create(['current_company_id' => $companyA->id]);
        $memberA->companies()->attach($companyA->id);
        $memberA->givePermissionTo('companies.view');

        Sanctum::actingAs($memberA);

        $this->getJson('/api/v1/companies')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $companyA->id);

        $this->getJson("/api/v1/companies/{$companyA->id}")
            ->assertOk()
            ->assertJsonPath('id', $companyA->id);

        $this->getJson("/api/v1/companies/{$companyB->id}")
            ->assertForbidden();
    }

    public function test_write_operations_require_authorization_and_company_membership(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $managerA = User::factory()->create(['current_company_id' => $companyA->id]);
        $managerA->companies()->attach($companyA->id);
        $managerA->givePermissionTo('companies.view');

        Sanctum::actingAs($managerA);

        $this->postJson('/api/v1/companies', [
            'name' => 'Nueva Empresa',
            'slug' => 'nueva-empresa',
        ])->assertForbidden();

        $this->putJson("/api/v1/companies/{$companyA->id}", [
            'name' => 'Intento sin permiso',
        ])->assertForbidden();

        $adminA = User::factory()->create(['current_company_id' => $companyA->id]);
        $adminA->companies()->attach($companyA->id);
        $adminA->givePermissionTo(['companies.view', 'companies.update']);

        Sanctum::actingAs($adminA);

        $this->putJson("/api/v1/companies/{$companyA->id}", [
            'name' => 'Empresa A Actualizada',
        ])->assertOk()->assertJsonPath('name', 'Empresa A Actualizada');

        $this->putJson("/api/v1/companies/{$companyB->id}", [
            'name' => 'Intento sobre B',
        ])->assertForbidden();
    }

    public function test_super_admin_keeps_global_api_access(): void
    {
        Role::findOrCreate('super_admin', 'web');

        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        Sanctum::actingAs($superAdmin);

        $this->getJson('/api/v1/companies')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson("/api/v1/companies/{$companyB->id}")
            ->assertOk()
            ->assertJsonPath('id', $companyB->id);

        $this->postJson('/api/v1/companies', [
            'name' => 'Creada por Super Admin',
            'slug' => 'creada-super-admin',
        ])->assertCreated();

        $this->putJson("/api/v1/companies/{$companyB->id}", [
            'name' => 'Empresa B por Super Admin',
        ])->assertOk()->assertJsonPath('name', 'Empresa B por Super Admin');
    }
}
