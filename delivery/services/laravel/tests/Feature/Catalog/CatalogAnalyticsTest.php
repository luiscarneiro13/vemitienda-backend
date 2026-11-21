<?php

namespace Tests\Feature\Catalog;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\CatalogEvent;
use App\Domain\Catalog\Models\Product;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /**
     * @return array{0: Company, 1: Branch, 2: Catalog}
     */
    private function makeStore(string $slug = 'tienda-test'): array
    {
        $company = Company::factory()->create([
            'name' => 'Tienda Test',
            'slug' => $slug,
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
        Product::factory()->create([
            'catalog_id' => $catalog->id,
            'catalog_category_id' => $category->id,
            'name' => 'Shawarma Mixto',
            'price' => 7.50,
            'is_active' => true,
        ]);

        return [$company, $branch, $catalog];
    }

    public function test_visiting_catalog_records_view_event_once_per_session(): void
    {
        [$company, $branch] = $this->makeStore();

        $this->assertSame(0, CatalogEvent::views()->count());

        $response = $this->get("/c/{$company->slug}/{$branch->uuid}");
        $response->assertOk();

        $this->assertSame(1, CatalogEvent::views()->count());
        $event = CatalogEvent::views()->first();
        $this->assertSame($company->id, $event->company_id);
        $this->assertSame($branch->id, $event->branch_id);
        $this->assertSame('view', $event->event_type);

        // Segunda visita en la misma sesión no duplica
        $this->get("/c/{$company->slug}/{$branch->uuid}")->assertOk();
        $this->assertSame(1, CatalogEvent::views()->count());
    }

    public function test_tracking_order_records_order_sent_event_with_cents_and_count(): void
    {
        [$company, $branch] = $this->makeStore();

        $this->assertSame(0, CatalogEvent::orders()->count());

        $response = $this->postJson("/c/{$company->slug}/{$branch->uuid}/track-order", [
            'total_cents' => 1500,
            'items_count' => 2,
        ]);

        $response->assertOk()->assertJson(['status' => 'ok']);

        $this->assertSame(1, CatalogEvent::orders()->count());
        $event = CatalogEvent::orders()->first();
        $this->assertSame($company->id, $event->company_id);
        $this->assertSame($branch->id, $event->branch_id);
        $this->assertSame('order_sent', $event->event_type);
        $this->assertSame(1500, $event->total_cents);
        $this->assertSame(2, $event->items_count);
    }

    public function test_track_order_validates_branch_availability(): void
    {
        [$company, $branch] = $this->makeStore();
        $branch->update(['status' => 'inactive']);

        $response = $this->postJson("/c/{$company->slug}/{$branch->uuid}/track-order", [
            'total_cents' => 1000,
            'items_count' => 1,
        ]);

        $response->assertStatus(404);
        $this->assertSame(0, CatalogEvent::count());
    }

    public function test_public_catalog_payload_includes_track_order_url(): void
    {
        [$company, $branch] = $this->makeStore();

        $response = $this->get("/c/{$company->slug}/{$branch->uuid}");
        $response->assertOk();
        $payload = $response->viewData('payload');

        $this->assertArrayHasKey('track_order_url', $payload);
        $this->assertStringContainsString("/c/{$company->slug}/{$branch->uuid}/track-order", $payload['track_order_url']);
    }

    public function test_stats_overview_widget_shows_metrics_for_super_admin(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        [$company, $branch] = $this->makeStore();

        CatalogEvent::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'event_type' => 'view',
            'created_at' => now(),
        ]);

        CatalogEvent::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'event_type' => 'order_sent',
            'total_cents' => 2500,
            'items_count' => 3,
            'created_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(StatsOverview::class)
            ->assertSee('Pedidos WhatsApp')
            ->assertSee('Ventas Estimadas')
            ->assertSee('Visitas al Catálogo')
            ->assertSee('Tasa de Conversión')
            ->assertSee('$ 25.00');
    }
}
