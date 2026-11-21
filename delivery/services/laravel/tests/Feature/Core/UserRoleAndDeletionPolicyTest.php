<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Companies\Models\Company;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRoleAndDeletionPolicyTest extends TestCase
{
    use RefreshDatabase;

    private Company $companyA;

    private Company $companyB;

    private User $superAdmin;

    private User $ownerA;

    private User $adminA;

    private User $userA;

    private User $ownerB;

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

        $this->companyA = Company::factory()->create();
        $this->companyB = Company::factory()->create();

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->ownerA = User::factory()->create([
            'company_id' => $this->companyA->id,
            'current_company_id' => $this->companyA->id,
        ]);
        $this->ownerA->companies()->attach($this->companyA->id, ['role' => 'owner']);
        $this->ownerA->assignRole('store_owner');

        $this->adminA = User::factory()->create([
            'company_id' => $this->companyA->id,
            'current_company_id' => $this->companyA->id,
        ]);
        $this->adminA->companies()->attach($this->companyA->id, ['role' => 'admin']);
        $this->adminA->assignRole('store_admin');

        $this->userA = User::factory()->create([
            'company_id' => $this->companyA->id,
            'current_company_id' => $this->companyA->id,
        ]);
        $this->userA->companies()->attach($this->companyA->id, ['role' => 'user']);
        $this->userA->assignRole('user');

        $this->ownerB = User::factory()->create([
            'company_id' => $this->companyB->id,
            'current_company_id' => $this->companyB->id,
        ]);
        $this->ownerB->companies()->attach($this->companyB->id, ['role' => 'owner']);
        $this->ownerB->assignRole('store_owner');
    }

    public function test_store_owner_and_store_admin_have_identical_permissions(): void
    {
        $ownerRole = Role::findByName('store_owner', 'web');
        $adminRole = Role::findByName('store_admin', 'web');

        $ownerPermissions = $ownerRole->permissions->pluck('name')->sort()->values()->all();
        $adminPermissions = $adminRole->permissions->pluck('name')->sort()->values()->all();

        $this->assertNotEmpty($ownerPermissions);
        $this->assertSame($ownerPermissions, $adminPermissions, 'store_owner y store_admin deben tener idénticos permisos.');
    }

    public function test_no_user_can_delete_themselves(): void
    {
        $this->assertFalse(Gate::forUser($this->superAdmin)->allows('delete', $this->superAdmin));
        $this->assertFalse(Gate::forUser($this->ownerA)->allows('delete', $this->ownerA));
        $this->assertFalse(Gate::forUser($this->adminA)->allows('delete', $this->adminA));
        $this->assertFalse(Gate::forUser($this->userA)->allows('delete', $this->userA));
    }

    public function test_store_owner_cannot_be_edited_by_store_owner_or_store_admin(): void
    {
        // El dueño de la tienda no lo puede editar el mismo dueño de la tienda
        $this->assertFalse(Gate::forUser($this->ownerA)->allows('update', $this->ownerA));

        // Tampoco lo puede editar un administrador de tienda
        $this->assertFalse(Gate::forUser($this->adminA)->allows('update', $this->ownerA));

        // Solo el super_admin puede editar al dueño de la tienda
        $this->assertTrue(Gate::forUser($this->superAdmin)->allows('update', $this->ownerA));
    }

    public function test_store_owner_can_only_be_deleted_by_super_admin(): void
    {
        // El dueño de la tienda no puede borrarse a sí mismo
        $this->assertFalse(Gate::forUser($this->ownerA)->allows('delete', $this->ownerA));

        // El administrador de la tienda no puede borrar al dueño
        $this->assertFalse(Gate::forUser($this->adminA)->allows('delete', $this->ownerA));

        // Solo el admin (super_admin) puede borrar al dueño de la tienda
        $this->assertTrue(Gate::forUser($this->superAdmin)->allows('delete', $this->ownerA));
    }

    public function test_store_owner_and_store_admin_can_manage_other_users_of_own_company(): void
    {
        // El dueño puede editar y borrar a administradores y usuarios de su empresa
        $this->assertTrue(Gate::forUser($this->ownerA)->allows('update', $this->adminA));
        $this->assertTrue(Gate::forUser($this->ownerA)->allows('delete', $this->adminA));
        $this->assertTrue(Gate::forUser($this->ownerA)->allows('update', $this->userA));
        $this->assertTrue(Gate::forUser($this->ownerA)->allows('delete', $this->userA));

        // El administrador de tienda puede editar y borrar a usuarios de su empresa
        $this->assertTrue(Gate::forUser($this->adminA)->allows('update', $this->userA));
        $this->assertTrue(Gate::forUser($this->adminA)->allows('delete', $this->userA));

        // Ninguno puede editar ni borrar a usuarios de otra empresa
        $this->assertFalse(Gate::forUser($this->ownerA)->allows('update', $this->ownerB));
        $this->assertFalse(Gate::forUser($this->ownerA)->allows('delete', $this->ownerB));
        $this->assertFalse(Gate::forUser($this->adminA)->allows('update', $this->ownerB));
        $this->assertFalse(Gate::forUser($this->adminA)->allows('delete', $this->ownerB));
    }
}
