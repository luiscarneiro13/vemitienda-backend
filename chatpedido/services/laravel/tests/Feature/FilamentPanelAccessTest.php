<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'manager', 'store_owner', 'store_admin', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    public function test_super_admin_can_access_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->assertTrue($superAdmin->canAccessPanel($panel));
        $this->actingAs($superAdmin)->get('/admin')->assertOk();
    }

    public function test_authorized_roles_can_access_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');

        foreach (['manager', 'store_owner', 'store_admin'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->assertTrue($user->canAccessPanel($panel), "El rol {$role} debe acceder al panel.");
            $this->actingAs($user)->get('/admin')->assertOk();
        }
    }

    public function test_unauthorized_users_cannot_access_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $basicUser = User::factory()->create();
        $basicUser->assignRole('user');

        $rolelessUser = User::factory()->create();

        $this->assertFalse($basicUser->canAccessPanel($panel));
        $this->assertFalse($rolelessUser->canAccessPanel($panel));

        $this->actingAs($basicUser)->get('/admin')->assertForbidden();
        $this->actingAs($rolelessUser)->get('/admin')->assertForbidden();
    }

    public function test_api_docs_navigation_item_is_only_visible_to_super_admin(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Documentación API')
            ->assertDontSee('Categorías')
            ->assertDontSee('Productos');

        $owner = User::factory()->create();
        $owner->assignRole('store_owner');

        $this->actingAs($owner)
            ->get('/admin')
            ->assertOk()
            ->assertDontSee('Documentación API');
    }
}
