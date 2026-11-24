<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Domain\Catalog\Models\CatalogEvent;
use App\Domain\Catalog\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StoreMetricsResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StoreMetricsController extends Controller
{
    /**
     * Consultar métricas y estadísticas agregadas de la tienda activa.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var Company $company */
        $company = app(TenantContext::class)->company();

        if (! $company) {
            abort(404, 'No se encontró la tienda activa.');
        }

        Gate::authorize('view', $company);

        $eventsSummary = CatalogEvent::query()
            ->where('company_id', $company->id)
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN event_type = 'order_sent' THEN 1 ELSE 0 END), 0) as total_orders,
                COALESCE(SUM(CASE WHEN event_type = 'view' THEN 1 ELSE 0 END), 0) as total_views,
                COALESCE(SUM(CASE WHEN event_type = 'order_sent' THEN total_cents ELSE 0 END), 0) as total_cents,
                MAX(CASE WHEN event_type = 'order_sent' THEN created_at END) as last_order"
            )
            ->first();

        $totalOrders = (int) ($eventsSummary->total_orders ?? 0);
        $totalViews = (int) ($eventsSummary->total_views ?? 0);
        $totalCents = (int) ($eventsSummary->total_cents ?? 0);
        $convRate = $totalViews > 0 ? round(($totalOrders / $totalViews) * 100, 1) : 0.0;
        $avgTicket = $totalOrders > 0 ? round(($totalCents / 100) / $totalOrders, 2) : 0.0;
        $lastOrder = $eventsSummary?->last_order ? Carbon::parse($eventsSummary->last_order)->toISOString() : null;

        $productSummary = Product::withoutTenancy()
            ->where('company_id', $company->id)
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(CASE WHEN is_active THEN 1 ELSE 0 END), 0) as active')
            ->first();

        $branch = $company->branches()->withoutTenancy()->first();

        $metrics = [
            'currency' => $company->currency ?? 'USD',
            'total_orders' => $totalOrders,
            'total_views' => $totalViews,
            'total_cents' => $totalCents,
            'conversion_rate' => $convRate,
            'average_ticket' => $avgTicket,
            'last_order_at' => $lastOrder,
            'products_count' => (int) ($productSummary->total ?? 0),
            'active_products' => (int) ($productSummary->active ?? 0),
            'public_catalog_url' => $branch?->publicCatalogUrl(),
        ];

        return response()->json([
            'data' => new StoreMetricsResource($metrics),
        ]);
    }
}
