<?php

namespace App\Filament\Resources\Companies\Tables;

use App\Core\Tenancy\TenantContext;
use App\Domain\Catalog\Models\CatalogEvent;
use App\Domain\Catalog\Models\Product;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('whatsapp_orders_count')
                    ->label('Pedidos WA')
                    ->counts(['catalogEvents as whatsapp_orders_count' => fn ($q) => $q->where('event_type', 'order_sent')])
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('views_count')
                    ->label('Visitas')
                    ->counts(['catalogEvents as views_count' => fn ($q) => $q->where('event_type', 'view')])
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('estimated_sales_cents')
                    ->label('Ventas Est.')
                    ->sum(['catalogEvents as estimated_sales_cents' => fn ($q) => $q->where('event_type', 'order_sent')], 'total_cents')
                    ->default(0)
                    ->formatStateUsing(fn ($state): string => '$ '.number_format(((int) $state) / 100, 2))
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tax_id')
                    ->label('RIF')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('switchCompany')
                    ->label(fn ($record): string => app(TenantContext::class)->companyId() === $record->id ? 'Tienda activa' : 'Usar tienda')
                    ->icon(fn ($record): string => app(TenantContext::class)->companyId() === $record->id ? 'heroicon-o-check-circle' : 'heroicon-o-arrows-right-left')
                    ->color(fn ($record): string => app(TenantContext::class)->companyId() === $record->id ? 'success' : 'gray')
                    ->disabled(fn ($record): bool => app(TenantContext::class)->companyId() === $record->id)
                    ->visible(fn (): bool => auth()->check() && ! auth()->user()?->hasRole('super_admin'))
                    ->action(function ($record): void {
                        /** @var User $user */
                        $user = auth()->user();
                        app(TenantContext::class)->switchTo($user, $record);

                        Notification::make()
                            ->title('Tienda activa: '.$record->name)
                            ->success()
                            ->send();
                    }),
                Action::make('metrics')
                    ->label('Estadísticas')
                    ->icon('heroicon-o-chart-bar')
                    ->color('primary')
                    ->modalHeading(fn ($record) => 'Estadísticas · '.$record->name)
                    ->modalDescription('Métricas de uso de clientes y actividad de la tienda')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function ($record) {
                        $eventsSummary = CatalogEvent::query()
                            ->where('company_id', $record->id)
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
                        $convRate = $totalViews > 0 ? round(($totalOrders / $totalViews) * 100, 1) : 0;
                        $avgTicket = $totalOrders > 0 ? round(($totalCents / 100) / $totalOrders, 2) : 0;
                        $lastOrder = $eventsSummary?->last_order ? Carbon::parse($eventsSummary->last_order) : null;

                        $productSummary = Product::withoutTenancy()
                            ->where('company_id', $record->id)
                            ->selectRaw('COUNT(*) as total, COALESCE(SUM(CASE WHEN is_active THEN 1 ELSE 0 END), 0) as active')
                            ->first();

                        $productsCount = (int) ($productSummary->total ?? 0);
                        $activeProducts = (int) ($productSummary->active ?? 0);

                        $branch = $record->branches()->withoutTenancy()->with(['company:id,slug', 'cityRecord:id,name'])->first();
                        $catalogUrl = $branch?->publicCatalogUrl();

                        return view('filament.pages.company-metrics-modal', compact(
                            'record',
                            'totalOrders',
                            'totalViews',
                            'totalCents',
                            'convRate',
                            'avgTicket',
                            'lastOrder',
                            'productsCount',
                            'activeProducts',
                            'branch',
                            'catalogUrl'
                        ));
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
