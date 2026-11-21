<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function store(array $attributes = []): Company
    {
        $company = Company::factory()->create($attributes);
        Branch::factory()->create(['company_id' => $company->id]);

        return $company;
    }

    public function test_directory_paginates_and_links_to_the_exact_branch(): void
    {
        for ($i = 1; $i <= 14; $i++) {
            $this->store(['name' => sprintf('Tienda %02d', $i)]);
        }
        $response = $this->get('/')->assertOk();
        $stores = $response->viewData('stores');
        $this->assertSame(14, $stores->total());
        $this->assertCount(12, $stores);
        $response->assertSee($stores->first()->branches->sole()->publicCatalogUrl(), false);
        $response->assertSee(route('filament.admin.auth.login'), false);
        $this->get('/?page=2')->assertOk()->assertSee('Tienda 13')->assertDontSee('Tienda 01');
    }

    public function test_search_and_category_filter_combine_and_persist_in_pagination(): void
    {
        for ($i = 1; $i <= 13; $i++) {
            $this->store(['name' => "Pizza Test $i", 'store_category' => 'Pizza']);
        }
        $this->store(['name' => 'Pizza Ajena', 'store_category' => 'Otra']);
        $response = $this->get('/?q=Pizza&category=Pizza')->assertOk();

        $this->assertSame(13, $response->viewData('stores')->total());
        $response->assertDontSee('Pizza Ajena');
        $this->assertStringContainsString('category=Pizza', $response->viewData('stores')->nextPageUrl());
        $this->assertStringContainsString('q=Pizza', $response->viewData('stores')->nextPageUrl());
        $this->get('/?q=NoExiste')->assertOk()->assertSee('No encontramos tiendas');
    }

    public function test_unavailable_and_ambiguous_stores_are_not_listed(): void
    {
        $this->store(['name' => 'Tienda visible']);
        $this->store(['name' => 'Empresa inactiva', 'is_active' => false]);
        $this->store(['name' => 'Estado inactivo', 'status' => 'inactive']);
        $this->store(['name' => 'Eliminada'])->delete();
        Company::factory()->create(['name' => 'Sin sucursal']);
        $this->store(['name' => 'Sin WhatsApp'])->branches()->first()->update(['whatsapp_number' => null]);
        $this->store(['name' => 'Sucursal inactiva'])->branches()->first()->update(['status' => 'inactive']);
        $this->store(['name' => 'Catalogo inactivo'])->defaultCatalog()->first()->update(['is_active' => false]);
        $this->store(['name' => 'Sin catalogo'])->defaultCatalog()->delete();
        config(['core.branches_per_company' => 2]);
        $legacy = $this->store(['name' => 'Ambigua']);
        Branch::factory()->create(['company_id' => $legacy->id]);
        config(['core.branches_per_company' => 1]);

        $response = $this->get('/')->assertOk();
        $this->assertSame(['Tienda visible'], $response->viewData('stores')->pluck('name')->all());
        $this->assertSame(2, $legacy->branches()->count());
    }

    public function test_authenticated_owner_sees_public_stores_from_other_companies(): void
    {
        $own = $this->store(['name' => 'Propia']);
        $foreign = $this->store(['name' => 'Ajena']);
        $owner = User::factory()->create(['company_id' => $own->id, 'current_company_id' => $own->id]);
        $owner->companies()->attach($own->id, ['role' => 'owner']);
        $this->actingAs($owner);
        app(TenantContext::class)->switchTo($owner, $own);

        $response = $this->get('/')->assertOk()->assertSee('Propia')->assertSee('Ajena');
        $this->assertSame(2, $response->viewData('stores')->total());
        $response->assertDontSee($foreign->email);
    }

    public function test_content_is_escaped_and_invalid_cover_uses_local_placeholder(): void
    {
        $this->store(['name' => '<script>alert(1)</script>', 'cover_image_url' => 'javascript:alert(1)']);
        $this->get('/')->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('javascript:alert(1)', false)
            ->assertSee('store-placeholder.svg');
    }
}
