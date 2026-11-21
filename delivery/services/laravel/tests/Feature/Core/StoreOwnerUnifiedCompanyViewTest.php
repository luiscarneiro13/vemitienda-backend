<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Locations\Models\City;
use App\Filament\Resources\Branches\BranchResource;
use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class StoreOwnerUnifiedCompanyViewTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $storeOwner;

    private Company $company;

    private Branch $branch;

    private City $city;

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

        $this->city = City::factory()->create(['name' => 'Caracas', 'is_active' => true]);

        $this->company = Company::factory()->create([
            'name' => 'Burger Aurora',
            'address' => 'Av. Principal 123',
        ]);

        $this->branch = Branch::factory()->create([
            'company_id' => $this->company->id,
            'city_id' => $this->city->id,
            'whatsapp_number' => '584141112233',
            'address' => 'Av. Principal 123',
            'status' => 'active',
            'is_main' => true,
        ]);

        $this->superAdmin = User::factory()->create(['email' => 'admin@test.com']);
        $this->superAdmin->assignRole('super_admin');

        $this->storeOwner = User::factory()->create([
            'email' => 'owner@test.com',
            'company_id' => $this->company->id,
            'current_company_id' => $this->company->id,
        ]);
        $this->storeOwner->companies()->attach($this->company->id, ['role' => 'owner']);
        $this->storeOwner->assignRole('store_owner');
    }

    public function test_branch_navigation_is_hidden_for_store_owner_and_visible_for_super_admin(): void
    {
        $this->actingAs($this->storeOwner);
        $this->assertFalse(BranchResource::shouldRegisterNavigation());

        $this->actingAs($this->superAdmin);
        $this->assertTrue(BranchResource::shouldRegisterNavigation());
    }

    public function test_company_navigation_url_redirects_store_owner_directly_to_edit_store(): void
    {
        $this->actingAs($this->storeOwner);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $expectedUrl = CompanyResource::getUrl('edit', ['record' => $this->company->id]);
        $this->assertSame($expectedUrl, CompanyResource::getNavigationUrl());
    }

    public function test_store_owner_can_edit_company_and_syncs_branch_whatsapp_and_address(): void
    {
        $this->actingAs($this->storeOwner);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EditCompany::class, ['record' => $this->company->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'name' => 'Burger Aurora',
                'whatsapp_number' => '584141112233',
                'city_id' => $this->city->id,
            ])
            ->fillForm([
                'name' => 'Burger Aurora Prime',
                'whatsapp_number' => '584149998877',
                'address' => 'Calle 50 con Av. 10',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Burger Aurora Prime', $this->company->fresh()->name);
        $this->assertSame('Calle 50 con Av. 10', $this->company->fresh()->address);

        $branch = $this->branch->fresh();
        $this->assertSame('584149998877', $branch->whatsapp_number);
        $this->assertSame('Calle 50 con Av. 10', $branch->address);
    }
}
