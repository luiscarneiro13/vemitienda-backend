<?php

namespace Tests\Feature\Core;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function memberOf(Company $company): User
    {
        $user = User::factory()->create(['current_company_id' => $company->id]);
        $user->companies()->attach($company->id);

        return $user;
    }

    public function test_user_only_sees_members_of_active_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $viewer = $this->memberOf($companyA);
        $mate = $this->memberOf($companyA);
        $outsider = $this->memberOf($companyB);

        $this->actingAs($viewer);

        $ids = User::query()->pluck('id');

        $this->assertTrue($ids->contains($viewer->id));
        $this->assertTrue($ids->contains($mate->id));
        $this->assertFalse($ids->contains($outsider->id));
    }

    public function test_visibility_follows_active_company_for_multi_company_user(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $viewer = $this->memberOf($companyA);
        $viewer->companies()->attach($companyB->id);

        $mateA = $this->memberOf($companyA);
        $mateB = $this->memberOf($companyB);
        $shared = $this->memberOf($companyA);
        $shared->companies()->attach($companyB->id);

        $this->actingAs($viewer);

        $ids = User::query()->pluck('id');
        $this->assertTrue($ids->contains($mateA->id));
        $this->assertTrue($ids->contains($shared->id));
        $this->assertFalse($ids->contains($mateB->id));

        app(TenantContext::class)->switchTo($viewer->refresh(), $companyB);

        $ids = User::query()->pluck('id');
        $this->assertFalse($ids->contains($mateA->id));
        $this->assertTrue($ids->contains($mateB->id));
        $this->assertTrue($ids->contains($shared->id));
    }

    public function test_individual_operations_cannot_reach_users_of_another_company(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $viewer = $this->memberOf($companyA);
        $outsider = $this->memberOf($companyB);

        $this->actingAs($viewer);

        $this->assertNull(User::find($outsider->id));
        $this->assertSame(0, User::whereKey($outsider->id)->update(['name' => 'Hackeado']));
        $this->assertSame(0, User::whereKey($outsider->id)->delete());

        $this->assertTrue(
            User::withoutGlobalScope('companyMembership')->whereKey($outsider->id)->exists()
        );
        $this->assertNotSame('Hackeado', $outsider->refresh()->name);
    }

    public function test_super_admin_keeps_global_access(): void
    {
        Role::findOrCreate('super_admin', 'web');

        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $admin = $this->memberOf($companyA);
        $admin->assignRole('super_admin');

        $outsider = $this->memberOf($companyB);

        $this->actingAs($admin);

        $this->assertTrue(User::query()->pluck('id')->contains($outsider->id));
        $this->assertNotNull(User::find($outsider->id));
    }
}
