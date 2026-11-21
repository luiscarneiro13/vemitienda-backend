<?php

namespace Tests\Feature\Catalog;

use App\Domain\Locations\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CityResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('store_owner', 'web');
        Role::findOrCreate('user', 'web');
    }

    public function test_super_admin_can_list_create_and_edit_cities(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)->get('/admin/cities')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/cities/create')->assertOk();

        $city = City::factory()->create();

        $this->actingAs($superAdmin)->get("/admin/cities/{$city->id}/edit")->assertOk();
    }

    public function test_store_owner_cannot_access_cities(): void
    {
        $storeOwner = User::factory()->create();
        $storeOwner->assignRole('store_owner');

        $this->actingAs($storeOwner)->get('/admin/cities')->assertForbidden();
        $this->actingAs($storeOwner)->get('/admin/cities/create')->assertForbidden();
    }

    public function test_user_without_role_cannot_access_cities(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)->get('/admin/cities')->assertForbidden();
    }

    public function test_city_slug_is_generated_automatically(): void
    {
        $city = City::create(['name' => 'El Tigre']);

        $this->assertSame('el-tigre', $city->slug);
    }
}
