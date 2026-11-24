<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Locations\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Api\V1\Concerns\SeedsApiPermissions;
use Tests\TestCase;

class CatalogAndProductApiTest extends TestCase
{
    use RefreshDatabase, SeedsApiPermissions;

    private Company $company;

    private Catalog $catalog;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissionsAndRoles();

        $city = City::factory()->create();
        $this->company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $this->company->id, 'city_id' => $city->id]);

        $this->catalog = Catalog::firstOrCreate(
            ['company_id' => $this->company->id],
            ['name' => 'Catálogo Principal', 'is_active' => true]
        );

        $this->owner = User::factory()->create([
            'company_id' => $this->company->id,
            'current_company_id' => $this->company->id,
            'current_branch_id' => $branch->id,
        ]);
        $this->owner->companies()->sync([$this->company->id => ['role' => 'owner']]);
        $this->owner->assignRole('store_owner');
    }

    public function test_category_lifecycle_crud_and_validation(): void
    {
        Sanctum::actingAs($this->owner);

        // 1. Create category
        $createResponse = $this->postJson('/api/v1/categories', [
            'catalog_id' => $this->catalog->id,
            'name' => 'Pizzas Especiales',
            'icon' => '🍕',
            'is_active' => true,
        ]);
        $createResponse->assertCreated()
            ->assertJsonPath('data.name', 'Pizzas Especiales')
            ->assertJsonPath('data.icon', '🍕');

        $categoryId = $createResponse->json('data.id');

        // 2. Update category
        $updateResponse = $this->putJson("/api/v1/categories/{$categoryId}", [
            'name' => 'Pizzas Gourmet',
        ]);
        $updateResponse->assertOk()
            ->assertJsonPath('data.name', 'Pizzas Gourmet');

        // 3. Toggle active
        $toggleResponse = $this->patchJson("/api/v1/categories/{$categoryId}/toggle-active");
        $toggleResponse->assertOk()
            ->assertJsonPath('data.is_active', false);

        // 4. Create product attached to category
        $product = Product::create([
            'company_id' => $this->company->id,
            'catalog_id' => $this->catalog->id,
            'catalog_category_id' => $categoryId,
            'name' => 'Pizza Cuatro Quesos',
            'price' => 12.50,
            'is_active' => true,
        ]);

        // 5. Try to delete category with product -> fails with 422
        $deleteBlocked = $this->deleteJson("/api/v1/categories/{$categoryId}");
        $deleteBlocked->assertStatus(422)
            ->assertJsonValidationErrors(['category']);

        // 6. Delete product first, then delete category
        $product->delete();
        $deleteOk = $this->deleteJson("/api/v1/categories/{$categoryId}");
        $deleteOk->assertOk();
    }

    public function test_product_lifecycle_and_image_upload(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->owner);

        $category = CatalogCategory::create([
            'company_id' => $this->company->id,
            'catalog_id' => $this->catalog->id,
            'name' => 'Hamburguesas',
            'is_active' => true,
        ]);

        // 1. Create product
        $createResponse = $this->postJson('/api/v1/products', [
            'catalog_id' => $this->catalog->id,
            'catalog_category_id' => $category->id,
            'name' => 'Doble Carne',
            'price' => 8.99,
            'compare_at_price' => 10.99,
            'badge' => 'Popular',
            'is_active' => true,
            'is_available' => true,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.name', 'Doble Carne')
            ->assertJsonPath('data.price', 8.99);

        $productId = $createResponse->json('data.id');

        // 2. Upload product image
        $imageFile = UploadedFile::fake()->image('burger.jpg', 600, 600);
        $uploadResponse = $this->postJson("/api/v1/products/{$productId}/image", [
            'image' => $imageFile,
        ]);
        $uploadResponse->assertOk()
            ->assertJsonPath('data.has_uploaded_image', true);

        // 3. Toggle availability
        $toggleAvailResponse = $this->patchJson("/api/v1/products/{$productId}/toggle-available");
        $toggleAvailResponse->assertOk()
            ->assertJsonPath('data.is_available', false);

        // 4. Delete product
        $deleteResponse = $this->deleteJson("/api/v1/products/{$productId}");
        $deleteResponse->assertOk();
    }

    public function test_product_option_groups_and_options_management(): void
    {
        Sanctum::actingAs($this->owner);

        $category = CatalogCategory::create([
            'company_id' => $this->company->id,
            'catalog_id' => $this->catalog->id,
            'name' => 'Bebidas',
            'is_active' => true,
        ]);

        $product = Product::create([
            'company_id' => $this->company->id,
            'catalog_id' => $this->catalog->id,
            'catalog_category_id' => $category->id,
            'name' => 'Batido Natural',
            'price' => 4.00,
            'is_active' => true,
        ]);

        // 1. Create option group
        $groupResponse = $this->postJson("/api/v1/products/{$product->id}/option-groups", [
            'name' => 'Tipo de Leche',
            'selection_type' => 'single',
            'is_required' => true,
        ]);
        $groupResponse->assertCreated()
            ->assertJsonPath('data.name', 'Tipo de Leche')
            ->assertJsonPath('data.selection_type', 'single');

        $groupId = $groupResponse->json('data.id');

        // 2. Create option
        $optResponse = $this->postJson("/api/v1/option-groups/{$groupId}/options", [
            'name' => 'Leche de Almendras',
            'price' => 1.00,
            'is_active' => true,
        ]);
        $optResponse->assertCreated()
            ->assertJsonPath('data.name', 'Leche de Almendras')
            ->assertJsonPath('data.price_formatted', '1.00');

        $optionId = $optResponse->json('data.id');

        // 3. Toggle option active
        $toggleResponse = $this->patchJson("/api/v1/options/{$optionId}/toggle-active");
        $toggleResponse->assertOk()
            ->assertJsonPath('data.is_active', false);

        // 4. Delete option
        $delOptResponse = $this->deleteJson("/api/v1/options/{$optionId}");
        $delOptResponse->assertOk();

        // 5. Delete option group
        $delGroupResponse = $this->deleteJson("/api/v1/option-groups/{$groupId}");
        $delGroupResponse->assertOk();
    }
}
