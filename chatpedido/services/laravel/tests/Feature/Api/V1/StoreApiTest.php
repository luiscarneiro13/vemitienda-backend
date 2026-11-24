<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Locations\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Api\V1\Concerns\SeedsApiPermissions;
use Tests\TestCase;

class StoreApiTest extends TestCase
{
    use RefreshDatabase, SeedsApiPermissions;

    private Company $company;

    private Branch $branch;

    private City $city;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissionsAndRoles();

        $this->city = City::factory()->create(['name' => 'San Cristóbal', 'is_active' => true]);
        $this->company = Company::factory()->create([
            'name' => 'Burger House',
            'slug' => 'burger-house',
            'store_description' => 'Las mejores hamburguesas',
        ]);
        $this->branch = Branch::factory()->create([
            'company_id' => $this->company->id,
            'city_id' => $this->city->id,
            'name' => 'Sucursal Centro',
            'phone' => '+584121234567',
            'whatsapp_number' => '+584121234567',
            'address' => 'Av. 5 con Calle 10',
        ]);

        $this->owner = User::factory()->create([
            'company_id' => $this->company->id,
            'current_company_id' => $this->company->id,
            'current_branch_id' => $this->branch->id,
        ]);
        $this->owner->companies()->sync([$this->company->id => ['role' => 'owner']]);
        $this->owner->assignRole('store_owner');
    }

    public function test_get_store_returns_company_and_branch_details(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->getJson('/api/v1/store');

        $response->assertOk()
            ->assertJsonPath('data.name', 'Burger House')
            ->assertJsonPath('data.store_description', 'Las mejores hamburguesas')
            ->assertJsonPath('data.branch.name', 'Sucursal Centro')
            ->assertJsonPath('data.branch.whatsapp_number', '584121234567');
    }

    public function test_update_store_modifies_company_and_syncs_branch(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->putJson('/api/v1/store', [
            'name' => 'Burger House Deluxe',
            'store_description' => 'Sabor inigualable',
            'whatsapp_number' => '+584149876543',
            'address' => 'Nueva Dirección 123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Burger House Deluxe')
            ->assertJsonPath('data.store_description', 'Sabor inigualable')
            ->assertJsonPath('data.branch.whatsapp_number', '584149876543')
            ->assertJsonPath('data.branch.address', 'Nueva Dirección 123');

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Burger House Deluxe',
            'store_description' => 'Sabor inigualable',
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $this->branch->id,
            'whatsapp_number' => '584149876543',
            'address' => 'Nueva Dirección 123',
        ]);
    }

    public function test_upload_and_delete_store_logo(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->owner);

        $file = UploadedFile::fake()->image('logo.png', 400, 400);

        $uploadResponse = $this->postJson('/api/v1/store/logo', [
            'logo' => $file,
        ]);

        $uploadResponse->assertOk();
        $this->assertNotNull($this->company->fresh()->logo);
        Storage::disk('public')->assertExists($this->company->fresh()->logo);

        $deleteResponse = $this->deleteJson('/api/v1/store/logo');
        $deleteResponse->assertOk();
        $this->assertNull($this->company->fresh()->logo);
    }

    public function test_get_store_metrics_returns_aggregated_counts(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->getJson('/api/v1/store/metrics');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'currency',
                    'total_orders',
                    'total_views',
                    'products_count',
                    'active_products',
                    'public_catalog_url',
                ],
            ]);
    }

    public function test_catalog_customization_endpoints(): void
    {
        Sanctum::actingAs($this->owner);

        // List templates
        $templatesResponse = $this->getJson('/api/v1/catalog-customization/templates');
        $templatesResponse->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'description']]]);

        // Get current customization
        $getResponse = $this->getJson('/api/v1/catalog-customization');
        $getResponse->assertOk()
            ->assertJsonStructure(['data' => ['template_id', 'primary_color', 'secondary_color']]);

        // Update customization
        $updateResponse = $this->putJson('/api/v1/catalog-customization', [
            'template_id' => 'compact_list',
            'primary_color' => '#f97316',
        ]);
        $updateResponse->assertOk()
            ->assertJsonPath('data.template_id', 'compact_list')
            ->assertJsonPath('data.primary_color', '#f97316');
    }
}
