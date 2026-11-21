<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Locations\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Api\V1\Concerns\SeedsApiPermissions;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, SeedsApiPermissions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissionsAndRoles();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_plain_user_cannot_login_to_api(): void
    {
        $user = User::factory()->create([
            'email' => 'plain@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('user');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'plain@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'No tienes permisos para acceder a la aplicación móvil de gestión.']);
    }

    public function test_store_owner_can_login_and_receives_token_and_active_company(): void
    {
        $company = Company::factory()->create(['name' => 'Pizza Express', 'slug' => 'pizza-express']);
        $city = City::factory()->create();
        Branch::factory()->create(['company_id' => $company->id, 'city_id' => $city->id]);

        $owner = User::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'current_company_id' => $company->id,
        ]);
        $owner->companies()->sync([$company->id => ['role' => 'owner']]);
        $owner->assignRole('store_owner');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'owner@example.com',
            'password' => 'password123',
            'device_name' => 'Pixel 8 Pro',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email', 'roles'],
                'active_store' => ['id', 'name', 'slug'],
                'stores',
            ]);

        $this->assertNotEmpty($response->json('access_token'));
        $this->assertEquals('Pizza Express', $response->json('active_store.name'));
    }

    public function test_me_returns_authenticated_user_profile(): void
    {
        $company = Company::factory()->create();
        $city = City::factory()->create();
        Branch::factory()->create(['company_id' => $company->id, 'city_id' => $city->id]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'current_company_id' => $company->id,
        ]);
        $user->companies()->sync([$company->id => ['role' => 'owner']]);
        $user->assignRole('store_owner');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonPath('active_store.id', $company->id);
    }

    public function test_switch_company_changes_active_company(): void
    {
        $company1 = Company::factory()->create(['name' => 'Tienda Uno']);
        $company2 = Company::factory()->create(['name' => 'Tienda Dos']);
        $city = City::factory()->create();
        Branch::factory()->create(['company_id' => $company1->id, 'city_id' => $city->id]);
        Branch::factory()->create(['company_id' => $company2->id, 'city_id' => $city->id]);

        $user = User::factory()->create([
            'company_id' => $company1->id,
            'current_company_id' => $company1->id,
        ]);
        $user->companies()->sync([
            $company1->id => ['role' => 'owner'],
            $company2->id => ['role' => 'owner'],
        ]);
        $user->assignRole('store_owner');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/switch-company', [
            'company_id' => $company2->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('active_store.id', $company2->id);

        $this->assertEquals($company2->id, $user->fresh()->current_company_id);
    }

    public function test_switch_company_fails_for_non_member_company(): void
    {
        $company1 = Company::factory()->create();
        $companyForbidden = Company::factory()->create();

        $user = User::factory()->create([
            'company_id' => $company1->id,
            'current_company_id' => $company1->id,
        ]);
        $user->companies()->sync([$company1->id => ['role' => 'owner']]);
        $user->assignRole('store_owner');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/switch-company', [
            'company_id' => $companyForbidden->id,
        ]);

        $response->assertStatus(403);
    }
}
