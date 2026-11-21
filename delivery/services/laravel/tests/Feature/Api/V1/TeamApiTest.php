<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Locations\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Api\V1\Concerns\SeedsApiPermissions;
use Tests\TestCase;

class TeamApiTest extends TestCase
{
    use RefreshDatabase, SeedsApiPermissions;

    private Company $company;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissionsAndRoles();

        $city = City::factory()->create();
        $this->company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $this->company->id, 'city_id' => $city->id]);

        $this->owner = User::factory()->create([
            'name' => 'Dueño Original',
            'email' => 'owner@example.com',
            'company_id' => $this->company->id,
            'current_company_id' => $this->company->id,
            'current_branch_id' => $branch->id,
        ]);
        $this->owner->companies()->sync([$this->company->id => ['role' => 'owner']]);
        $this->owner->assignRole('store_owner');
    }

    public function test_list_team_members_returns_members_and_excludes_super_admin(): void
    {
        $superAdmin = User::factory()->create(['email' => 'admin@example.com']);
        $superAdmin->assignRole('super_admin');
        $superAdmin->companies()->sync([$this->company->id => ['role' => 'admin']]);

        $admin = User::factory()->create(['name' => 'Admin Tienda']);
        $admin->assignRole('store_admin');
        $admin->companies()->sync([$this->company->id => ['role' => 'admin']]);

        Sanctum::actingAs($this->owner);

        $response = $this->getJson('/api/v1/team');

        $response->assertOk();
        $emails = collect($response->json('data'))->pluck('email')->all();

        $this->assertContains($this->owner->email, $emails);
        $this->assertContains($admin->email, $emails);
        $this->assertNotContains('admin@example.com', $emails);
    }

    public function test_owner_can_create_new_team_member(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->postJson('/api/v1/team', [
            'name' => 'Carlos Administrador',
            'email' => 'carlos@tienda.com',
            'password' => 'password123',
            'role' => 'store_admin',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Carlos Administrador')
            ->assertJsonPath('data.email', 'carlos@tienda.com');

        $newUser = User::where('email', 'carlos@tienda.com')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->hasRole('store_admin'));
        $this->assertTrue($newUser->companies()->where('companies.id', $this->company->id)->exists());
    }

    public function test_cannot_create_member_with_forbidden_role(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->postJson('/api/v1/team', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'role' => 'super_admin',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_owner_cannot_delete_self(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->deleteJson("/api/v1/team/{$this->owner->id}");

        $response->assertForbidden();
    }
}
