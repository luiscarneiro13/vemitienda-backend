<?php

namespace App\Filament\Widgets;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\CatalogEvent;
use App\Domain\Catalog\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        $isSuperAdmin = $user->hasRole('super_admin');
        $companyIds = $isSuperAdmin ? null : $user->companies()->pluck('companies.id');
        $todayStart = now()->startOfDay()->toDateTimeString();

        $summary = CatalogEvent::query()
            ->when(! $isSuperAdmin, fn ($q) => $q->whereIn('company_id', $companyIds))
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN event_type = 'order_sent' THEN 1 ELSE 0 END), 0) as total_orders,
                COALESCE(SUM(CASE WHEN event_type = 'order_sent' AND created_at >= ? THEN 1 ELSE 0 END), 0) as today_orders,
                COALESCE(SUM(CASE WHEN event_type = 'order_sent' THEN total_cents ELSE 0 END), 0) as total_cents,
                COALESCE(SUM(CASE WHEN event_type = 'order_sent' AND created_at >= ? THEN total_cents ELSE 0 END), 0) as today_cents,
                COALESCE(SUM(CASE WHEN event_type = 'view' THEN 1 ELSE 0 END), 0) as total_views,
                COALESCE(SUM(CASE WHEN event_type = 'view' AND created_at >= ? THEN 1 ELSE 0 END), 0) as today_views,
                COUNT(DISTINCT CASE WHEN event_type = 'order_sent' THEN company_id END) as active_stores",
                [$todayStart, $todayStart, $todayStart]
            )
            ->first();

        $totalOrders = (int) ($summary->total_orders ?? 0);
        $todayOrders = (int) ($summary->today_orders ?? 0);
        $totalCents = (int) ($summary->total_cents ?? 0);
        $todayCents = (int) ($summary->today_cents ?? 0);
        $totalViews = (int) ($summary->total_views ?? 0);
        $todayViews = (int) ($summary->today_views ?? 0);

        $conversionRate = $totalViews > 0 ? round(($totalOrders / $totalViews) * 100, 1) : 0;

        $stats = [
            Stat::make('Pedidos WhatsApp', number_format($totalOrders))
                ->description("Hoy: {$todayOrders} pedidos")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-chat-bubble-left-right'),

            Stat::make('Ventas Estimadas', '$ '.number_format($totalCents / 100, 2))
                ->description('Hoy: $ '.number_format($todayCents / 100, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Visitas al Catálogo', number_format($totalViews))
                ->description("Hoy: {$todayViews} visitas")
                ->color('info')
                ->icon('heroicon-o-eye'),

            Stat::make('Tasa de Conversión', "{$conversionRate}%")
                ->description('Visitas a pedido por WhatsApp')
                ->color($conversionRate >= 10 ? 'success' : 'gray')
                ->icon('heroicon-o-arrow-path-rounded-square'),
        ];

        if ($isSuperAdmin) {
            $activeStores = (int) ($summary->active_stores ?? 0);
            $totalStores = Company::query()->where('status', 'active')->count();

            $stats[] = Stat::make('Tiendas con Ventas', "{$activeStores} / {$totalStores}")
                ->description('Tiendas con al menos un pedido')
                ->color('warning')
                ->icon('heroicon-o-building-storefront');
        } else {
            $productSummary = Product::whereIn('company_id', $companyIds)
                ->selectRaw('COUNT(*) as total, COALESCE(SUM(CASE WHEN is_active THEN 1 ELSE 0 END), 0) as active')
                ->first();

            $activeProducts = (int) ($productSummary->active ?? 0);
            $totalProducts = (int) ($productSummary->total ?? 0);

            $stats[] = Stat::make('Productos Activos', "{$activeProducts} / {$totalProducts}")
                ->description('Productos visibles en tu menú')
                ->color('primary')
                ->icon('heroicon-o-tag');
        }

        return $stats;
    }
}
