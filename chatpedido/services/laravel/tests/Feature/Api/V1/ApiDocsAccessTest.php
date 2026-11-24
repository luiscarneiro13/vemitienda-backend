<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDocsAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_unauthenticated_user_cannot_access_api_docs(): void
    {
        $response = $this->get('/docs');
        $response->assertRedirect('/admin/login');

        $jsonResponse = $this->get('/docs.json');
        $jsonResponse->assertRedirect('/admin/login');
    }

    public function test_store_owner_and_admin_cannot_access_api_docs(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('store_owner');

        $this->actingAs($owner)->get('/docs')->assertForbidden();
        $this->actingAs($owner)->get('/docs.json')->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('store_admin');

        $this->actingAs($admin)->get('/docs')->assertForbidden();
        $this->actingAs($admin)->get('/docs.json')->assertForbidden();
    }

    public function test_super_admin_can_access_api_docs(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get('/docs');
        $response->assertOk();

        $jsonResponse = $this->actingAs($superAdmin)->get('/docs.json');
        $jsonResponse->assertOk();
    }
}
