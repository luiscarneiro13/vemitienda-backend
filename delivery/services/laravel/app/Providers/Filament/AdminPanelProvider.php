<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\StatsOverview;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('ChatPedido')
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('favicon.svg'))
            ->font('DM Sans')
            ->colors([
                'primary' => [
                    50 => '#faf9fc',
                    100 => '#eee8fb',
                    200 => '#ded2f7',
                    300 => '#c2abf0',
                    400 => '#9f7be5',
                    500 => '#7b4fd4',
                    600 => '#6135bc',
                    700 => '#4c269c',
                    800 => '#3e207e',
                    900 => '#331c66',
                    950 => '#242034',
                ],
                'gray' => [
                    50 => '#faf9fc',
                    100 => '#f3f0f8',
                    200 => '#e3deec',
                    300 => '#c5bed2',
                    400 => '#948ca4',
                    500 => '#665f74',
                    600 => '#4f485e',
                    700 => '#3c364b',
                    800 => '#2e293d',
                    900 => '#242034',
                    950 => '#171422',
                ],
                'warning' => [
                    50 => '#fff8f1',
                    100 => '#ffeedb',
                    200 => '#ffd9b3',
                    300 => '#ffbe7b',
                    400 => '#ff9f45',
                    500 => '#f5821f',
                    600 => '#d9680d',
                    700 => '#b3500b',
                    800 => '#8f3f0f',
                    900 => '#743510',
                    950 => '#442609',
                ],
            ])
            ->navigationGroups([
                NavigationGroup::make('Empresa'),
                NavigationGroup::make('Catálogo'),
                NavigationGroup::make('Configuración')->collapsed(),
                NavigationGroup::make('Usuarios y Accesos')->collapsed(),
                NavigationGroup::make('Facturación')->collapsed(),
                NavigationGroup::make('Sistema')->collapsed(),
                NavigationGroup::make('Auditoría')->collapsed(),
            ])
            ->navigationItems([
                NavigationItem::make('Documentación API')
                    ->url('/docs', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-code-bracket')
                    ->group('Sistema')
                    ->sort(90)
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin') ?? false),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.render-hooks.sidebar-styles')->render(),
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
