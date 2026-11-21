<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CompanyProfileEditTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private Branch $branch;

    private User $owner;

    private User $admin;

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

        $this->company = Company::factory()->create([
            'name' => 'Burger Original',
            'tax_id' => 'J-11111111-1',
            'address' => 'Av. Antigua 123',
            'store_description' => 'Las mejores hamburguesas clásicas',
        ]);

        $this->branch = Branch::factory()->create([
            'company_id' => $this->company->id,
            'address' => '',
        ]);

        $this->owner = User::factory()->create([
            'company_id' => $this->company->id,
            'current_company_id' => $this->company->id,
        ]);
        $this->owner->companies()->attach($this->company->id, ['role' => 'owner']);
        $this->owner->assignRole('store_owner');

        $this->admin = User::factory()->create([
            'company_id' => $this->company->id,
            'current_company_id' => $this->company->id,
        ]);
        $this->admin->companies()->attach($this->company->id, ['role' => 'admin']);
        $this->admin->assignRole('store_admin');
    }

    public function test_store_owner_can_edit_company_name_rif_address_and_description(): void
    {
        Storage::fake('public');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($this->owner);

        Livewire::test(EditCompany::class, ['record' => $this->company->getRouteKey()])
            ->assertSuccessful()
            ->fillForm([
                'name' => 'Burger Renovada',
                'tax_id' => 'J-99999999-9',
                'address' => 'C.C. Sambil, Nivel Feria, Local 12',
                'store_description' => 'Especialistas en smash burgers y papas trufadas',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->company->refresh();
        $this->assertSame('Burger Renovada', $this->company->name);
        $this->assertSame('J-99999999-9', $this->company->tax_id);
        $this->assertSame('C.C. Sambil, Nivel Feria, Local 12', $this->company->address);
        $this->assertSame('Especialistas en smash burgers y papas trufadas', $this->company->store_description);

        $this->branch->refresh();
        $this->assertSame('C.C. Sambil, Nivel Feria, Local 12', $this->branch->address);
    }

    public function test_store_admin_can_also_edit_company_details(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($this->admin);

        Livewire::test(EditCompany::class, ['record' => $this->company->getRouteKey()])
            ->assertSuccessful()
            ->fillForm([
                'name' => 'Burger Admin Edition',
                'tax_id' => 'J-88888888-8',
                'address' => 'Calle Los Ángeles 456',
                'store_description' => 'Nueva administración con mismo sabor',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->company->refresh();
        $this->assertSame('Burger Admin Edition', $this->company->name);
        $this->assertSame('J-88888888-8', $this->company->tax_id);
        $this->assertSame('Calle Los Ángeles 456', $this->company->address);
        $this->assertSame('Nueva administración con mismo sabor', $this->company->store_description);
    }

    public function test_store_owner_cannot_change_company_slug(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($this->owner);

        $originalSlug = $this->company->slug;

        Livewire::test(EditCompany::class, ['record' => $this->company->getRouteKey()])
            ->assertFormFieldIsDisabled('slug');

        $this->company->refresh();
        $this->assertSame($originalSlug, $this->company->slug);
    }
}
