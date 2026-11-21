<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /**
     * @return array{0: Company, 1: Branch, 2: Catalog, 3: CatalogCategory, 4: Product, 5: Product}
     */
    private function makeStore(): array
    {
        $company = Company::factory()->create([
            'name' => 'Tienda A',
            'slug' => 'tienda-a',
            'is_active' => true,
            'status' => 'active',
        ]);
        $branch = Branch::factory()->create([
            'company_id' => $company->id,
            'name' => 'Sucursal Centro',
            'whatsapp_number' => '584141111111',
            'status' => 'active',
        ]);
        $catalog = $company->defaultCatalog()->withoutTenancy()->first()
            ?? Catalog::factory()->create(['company_id' => $company->id, 'is_default' => true, 'is_active' => true]);
        $category = CatalogCategory::factory()->create(['catalog_id' => $catalog->id, 'is_active' => true]);
        $visible = Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
            'name' => 'Producto Visible',
            'is_active' => true,
            'is_available' => true,
        ]);
        $hidden = Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
            'name' => 'Producto Oculto',
            'is_active' => false,
        ]);

        return [$company, $branch, $catalog, $category, $visible, $hidden];
    }

    private function payloadOf($response): array
    {
        return $response->original->getData()['payload'];
    }

    private function productNames(array $payload): array
    {
        return collect($payload['categories'])
            ->flatMap(fn ($category) => collect($category['products'])->pluck('name'))
            ->all();
    }

    public function test_valid_link_shows_catalog(): void
    {
        [$company, $branch] = $this->makeStore();

        $response = $this->get("/c/tienda-a/{$branch->uuid}");

        $response->assertOk();
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $response->assertViewHas('payload');

        $payload = $this->payloadOf($response);
        $this->assertSame('Tienda A', $payload['store']['name']);
        $this->assertSame('584141111111', $payload['branch']['whatsapp']);
        $this->assertContains('Producto Visible', $this->productNames($payload));
        $this->assertNotContains('Producto Oculto', $this->productNames($payload));
    }

    public function test_link_without_branch_uuid_shows_error(): void
    {
        $this->makeStore();

        $this->get('/c/tienda-a')->assertNotFound()->assertSee('Este enlace no es válido', false);
    }

    public function test_link_with_malformed_uuid_shows_error(): void
    {
        $this->makeStore();

        $this->get('/c/tienda-a/123')->assertNotFound();
    }

    public function test_link_with_extra_segments_shows_error(): void
    {
        [, $branch] = $this->makeStore();

        $this->get("/c/tienda-a/{$branch->uuid}/extra")->assertNotFound();
    }

    public function test_branch_of_another_store_shows_error(): void
    {
        [$companyA] = $this->makeStore();
        $companyB = Company::factory()->create(['slug' => 'tienda-b', 'status' => 'active', 'is_active' => true]);
        $branchB = Branch::factory()->create(['company_id' => $companyB->id, 'whatsapp_number' => '584142222222']);
        Catalog::factory()->create(['company_id' => $companyB->id, 'is_default' => true]);

        $this->get("/c/{$companyA->slug}/{$branchB->uuid}")->assertNotFound();
    }

    public function test_unknown_uuid_shows_error(): void
    {
        $this->makeStore();

        $this->get('/c/tienda-a/'.Str::uuid())->assertNotFound();
    }

    public function test_inactive_branch_shows_error(): void
    {
        [, $branch] = $this->makeStore();
        $branch->update(['status' => 'inactive']);

        $this->get("/c/tienda-a/{$branch->uuid}")->assertNotFound();
    }

    public function test_branch_without_whatsapp_shows_error(): void
    {
        [, $branch] = $this->makeStore();
        $branch->update(['whatsapp_number' => null]);

        $this->get("/c/tienda-a/{$branch->uuid}")->assertNotFound();
    }

    public function test_inactive_company_shows_error(): void
    {
        [$company, $branch] = $this->makeStore();
        $company->update(['is_active' => false]);

        $this->get("/c/tienda-a/{$branch->uuid}")->assertNotFound();
    }

    public function test_company_with_inactive_status_shows_error(): void
    {
        [$company, $branch] = $this->makeStore();
        $company->update(['status' => 'inactive']);

        $this->get("/c/tienda-a/{$branch->uuid}")->assertNotFound();
    }

    public function test_soft_deleted_company_shows_error(): void
    {
        [$company, $branch] = $this->makeStore();
        $company->delete();

        $this->get("/c/tienda-a/{$branch->uuid}")->assertNotFound();
    }

    public function test_inactive_catalog_shows_error(): void
    {
        [, $branch, $catalog] = $this->makeStore();
        $catalog->update(['is_active' => false]);

        $this->get("/c/tienda-a/{$branch->uuid}")->assertNotFound();
    }

    public function test_unknown_slug_shows_error(): void
    {
        [, $branch] = $this->makeStore();

        $this->get("/c/tienda-desconocida/{$branch->uuid}")->assertNotFound();
    }

    public function test_catalog_is_visible_when_logged_in_as_owner_of_another_store(): void
    {
        [$companyA, $branchA] = $this->makeStore();

        // Tienda B con su propio dueño
        $companyB = Company::factory()->create(['status' => 'active', 'is_active' => true]);
        Catalog::factory()->create(['company_id' => $companyB->id, 'is_default' => true]);
        Permission::findOrCreate('ViewAny:Company', 'web');
        Role::findOrCreate('store_owner', 'web');
        $ownerB = User::factory()->create(['company_id' => $companyB->id, 'current_company_id' => $companyB->id]);
        $ownerB->companies()->attach($companyB->id, ['role' => 'owner']);
        $ownerB->assignRole('store_owner');

        $response = $this->actingAs($ownerB)->get("/c/{$companyA->slug}/{$branchA->uuid}");

        $response->assertOk();
        $this->assertContains('Producto Visible', $this->productNames($this->payloadOf($response)));
    }

    public function test_unavailable_product_is_sent_as_sold_out(): void
    {
        [, $branch, $catalog, $category] = $this->makeStore();
        $soldOut = Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
            'name' => 'Producto Agotado',
            'is_active' => true,
            'is_available' => false,
        ]);

        $response = $this->get("/c/tienda-a/{$branch->uuid}");
        $response->assertOk();

        $products = collect($this->payloadOf($response)['categories'])->flatMap(fn ($c) => $c['products']);
        $shown = $products->firstWhere('id', $soldOut->id);

        $this->assertNotNull($shown);
        $this->assertFalse($shown['is_available']);
    }

    public function test_empty_option_groups_are_not_sent(): void
    {
        [, $branch, , , $product] = $this->makeStore();

        $emptyGroup = ProductOptionGroup::factory()->create(['product_id' => $product->id, 'name' => 'GrupoVacio']);
        $inactiveOnly = ProductOptionGroup::factory()->create(['product_id' => $product->id, 'name' => 'SoloInactivos']);
        ProductOption::factory()->create(['product_option_group_id' => $inactiveOnly->id, 'is_active' => false]);

        $response = $this->get("/c/tienda-a/{$branch->uuid}");
        $products = collect($this->payloadOf($response)['categories'])->flatMap(fn ($c) => $c['products']);
        $shown = $products->firstWhere('id', $product->id);
        $groupIds = collect($shown['option_groups'])->pluck('id');

        $this->assertNotContains($emptyGroup->id, $groupIds);
        $this->assertNotContains($inactiveOnly->id, $groupIds);
    }

    public function test_payload_does_not_expose_company_id(): void
    {
        [, $branch] = $this->makeStore();

        $response = $this->get("/c/tienda-a/{$branch->uuid}");
        $response->assertOk();
        $response->assertDontSee('company_id', false);
        $response->assertDontSee('admin@example.com', false);
    }

    public function test_presenter_returns_prices_in_cents(): void
    {
        [, $branch, , , $product] = $this->makeStore();
        $product->update(['price' => 5.50]);

        $group = ProductOptionGroup::factory()->create(['product_id' => $product->id]);
        $option = ProductOption::factory()->create(['product_option_group_id' => $group->id, 'price' => 1.25]);

        $response = $this->get("/c/tienda-a/{$branch->uuid}");
        $products = collect($this->payloadOf($response)['categories'])->flatMap(fn ($c) => $c['products']);
        $shown = $products->firstWhere('id', $product->id);

        $this->assertSame(550, $shown['price_cents']);
        $optionShown = collect($shown['option_groups'])->flatMap(fn ($g) => $g['options'])->firstWhere('id', $option->id);
        $this->assertSame(125, $optionShown['price_cents']);
    }

    public function test_payload_includes_removal_flag_and_logo(): void
    {
        [$company, $branch, , , $product] = $this->makeStore();
        $company->update(['logo' => 'https://example.com/logo.png']);

        $removal = ProductOptionGroup::factory()->create([
            'product_id' => $product->id,
            'name' => 'Sin ingredientes',
            'is_removal' => true,
        ]);
        ProductOption::factory()->create(['product_option_group_id' => $removal->id, 'name' => 'Sin cebolla', 'price' => 0]);

        $normal = ProductOptionGroup::factory()->create(['product_id' => $product->id, 'name' => 'Extras']);
        ProductOption::factory()->create(['product_option_group_id' => $normal->id]);

        $response = $this->get("/c/tienda-a/{$branch->uuid}");
        $payload = $this->payloadOf($response);

        $this->assertSame('https://example.com/logo.png', $payload['store']['logo_url']);
        $this->assertSame('TI', $payload['store']['initials']);

        $shown = collect($payload['categories'])->flatMap(fn ($c) => $c['products'])->firstWhere('id', $product->id);
        $groups = collect($shown['option_groups']);

        $this->assertTrue($groups->firstWhere('name', 'Sin ingredientes')['is_removal']);
        $this->assertFalse($groups->firstWhere('name', 'Extras')['is_removal']);
        $this->assertNull($shown['image_url']);
    }

    public function test_product_remote_image_url_is_exposed_and_unsafe_urls_ignored(): void
    {
        [, $branch, , , $product] = $this->makeStore();
        $product->update(['image_url' => 'https://images.unsplash.com/photo-123?w=700']);

        $response = $this->get("/c/tienda-a/{$branch->uuid}");
        $shown = collect($this->payloadOf($response)['categories'])->flatMap(fn ($c) => $c['products'])->firstWhere('id', $product->id);
        $this->assertSame('https://images.unsplash.com/photo-123?w=700', $shown['image_url']);

        $product->update(['image_url' => 'javascript:alert(1)']);
        $response = $this->get("/c/tienda-a/{$branch->uuid}");
        $shown = collect($this->payloadOf($response)['categories'])->flatMap(fn ($c) => $c['products'])->firstWhere('id', $product->id);
        $this->assertNull($shown['image_url']);
    }

    public function test_rate_limit_returns_429_after_60_requests(): void
    {
        [, $branch] = $this->makeStore();
        $url = "/c/tienda-a/{$branch->uuid}";

        for ($i = 0; $i < 60; $i++) {
            $this->get($url)->assertOk();
        }

        $this->get($url)->assertStatus(429);
    }

    public function test_branch_public_catalog_url_matches_route(): void
    {
        [, $branch] = $this->makeStore();

        $this->assertStringEndsWith("/c/tienda-a/{$branch->uuid}", $branch->publicCatalogUrl());
    }
}
