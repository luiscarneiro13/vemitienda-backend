<?php

namespace App\Filament\Pages;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Services\CompanySettingsManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CatalogCustomization extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.catalog-customization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Diseño y Plantilla';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public ?string $previewUrl = null;

    public ?Company $activeCompany = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->hasAnyRole(['super_admin', 'store_owner', 'store_admin']) ?? false;
    }

    public function mount(): void
    {
        $this->activeCompany = tenant()->company() ?? Company::query()->first();

        if (! $this->activeCompany) {
            Notification::make()
                ->title('No hay una tienda activa')
                ->body('Debes tener al menos una tienda registrada para personalizar su catálogo.')
                ->warning()
                ->send();

            return;
        }

        $branch = $this->activeCompany->branches()->withoutTenancy()->first();
        if ($branch && $this->activeCompany->slug && $branch->uuid) {
            $this->previewUrl = route('public.catalog.show', [
                'companySlug' => $this->activeCompany->slug,
                'branchUuid' => $branch->uuid,
            ]);
        }

        $settings = app(CompanySettingsManager::class)->group($this->activeCompany, 'catalog');

        $this->form->fill([
            'template_id' => $settings['catalog.template_id'] ?? $settings['template_id'] ?? 'grid_2col',
            'primary_color' => $settings['catalog.primary_color'] ?? $settings['primary_color'] ?? '#a04100',
            'secondary_color' => $settings['catalog.secondary_color'] ?? $settings['secondary_color'] ?? '#b7131a',
            'show_search' => (bool) ($settings['catalog.show_search'] ?? $settings['show_search'] ?? true),
            'show_delivery_time' => (bool) ($settings['catalog.show_delivery_time'] ?? $settings['show_delivery_time'] ?? true),
            'delivery_time_text' => (string) ($settings['catalog.delivery_time_text'] ?? $settings['delivery_time_text'] ?? '15-25 min'),
            'show_free_shipping_bar' => (bool) ($settings['catalog.show_free_shipping_bar'] ?? $settings['show_free_shipping_bar'] ?? false),
            'free_shipping_threshold' => (float) ($settings['catalog.free_shipping_threshold'] ?? $settings['free_shipping_threshold'] ?? 20.00),
            'show_promo_banner' => (bool) ($settings['catalog.show_promo_banner'] ?? $settings['show_promo_banner'] ?? false),
            'promo_title' => (string) ($settings['catalog.promo_title'] ?? $settings['promo_title'] ?? '¡20% OFF en tu primera compra!'),
            'promo_subtitle' => (string) ($settings['catalog.promo_subtitle'] ?? $settings['promo_subtitle'] ?? 'Usa el cupón en WhatsApp al confirmar tu pedido'),
            'promo_code' => (string) ($settings['catalog.promo_code'] ?? $settings['promo_code'] ?? 'DELIVERY20'),
            'show_featured_carousel' => (bool) ($settings['catalog.show_featured_carousel'] ?? $settings['show_featured_carousel'] ?? true),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('template_id')->required(),

                Section::make('Paleta de Colores de la Marca')
                    ->description('Personaliza los acentos principales para alinearlos con la identidad de tu negocio.')
                    ->columns(2)
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label('Color Primario')
                            ->helperText('Color principal para botones, destaques y acentos.')
                            ->required(),
                        ColorPicker::make('secondary_color')
                            ->label('Color Secundario')
                            ->helperText('Color de contraste para badges de descuento y ofertas.')
                            ->required(),
                    ]),

                Section::make('Búsqueda y Tiempos de Entrega')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_search')
                            ->label('Activar barra de búsqueda')
                            ->helperText('Permite al cliente filtrar platos por nombre e ingredientes.')
                            ->default(true),
                        Toggle::make('show_delivery_time')
                            ->label('Mostrar tiempo estimado de entrega')
                            ->default(true)
                            ->live(),
                        TextInput::make('delivery_time_text')
                            ->label('Texto de tiempo de entrega')
                            ->placeholder('Ej: 15-25 min, 30-45 min')
                            ->maxLength(50)
                            ->visible(fn (Get $get): bool => (bool) $get('show_delivery_time')),
                    ]),

                Section::make('Envío Gratis')
                    ->description('Muestra una barra interactiva de progreso hacia una meta de compra.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_free_shipping_bar')
                            ->label('Activar barra de envío gratis')
                            ->default(false)
                            ->live(),
                        TextInput::make('free_shipping_threshold')
                            ->label('Monto mínimo para envío gratis')
                            ->numeric()
                            ->minValue(1)
                            ->step(0.50)
                            ->visible(fn (Get $get): bool => (bool) $get('show_free_shipping_bar')),
                    ]),

                Section::make('Banner Promocional y Cupones')
                    ->description('Destaca un aviso de oferta o cupón en la parte superior del catálogo.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('show_promo_banner')
                            ->label('Activar banner promocional')
                            ->default(false)
                            ->live(),
                        TextInput::make('promo_code')
                            ->label('Código de cupón (opcional)')
                            ->placeholder('Ej: PROMO20, BIENVENIDO')
                            ->maxLength(30)
                            ->visible(fn (Get $get): bool => (bool) $get('show_promo_banner')),
                        TextInput::make('promo_title')
                            ->label('Título de la promoción')
                            ->placeholder('Ej: ¡20% OFF en tu primera compra!')
                            ->maxLength(100)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => (bool) $get('show_promo_banner')),
                        TextInput::make('promo_subtitle')
                            ->label('Subtítulo o condiciones')
                            ->placeholder('Ej: Válido en pedidos superiores a $15 por WhatsApp')
                            ->maxLength(200)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => (bool) $get('show_promo_banner')),
                    ]),

                Section::make('Productos Destacados')
                    ->schema([
                        Toggle::make('show_featured_carousel')
                            ->label('Mostrar carrusel de productos destacados')
                            ->helperText('Muestra una fila horizontal de los platos marcados como "Destacados" en el catálogo.')
                            ->default(true),
                    ]),
            ])
            ->statePath('data');
    }

    public function selectTemplate(string $templateId): void
    {
        $this->data['template_id'] = $templateId;
    }

    public function save(): void
    {
        if (! $this->activeCompany) {
            return;
        }

        $state = $this->form->getState();
        $manager = app(CompanySettingsManager::class);

        $settings = [];
        foreach ($state as $key => $value) {
            $settings['catalog.'.$key] = $value;
        }

        $manager->setMany($this->activeCompany, $settings, 'catalog');

        Notification::make()
            ->title('Diseño actualizado con éxito')
            ->body('Los cambios ya están visibles en el catálogo público.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return array_filter([
            $this->previewUrl ? Action::make('preview')
                ->label('Ver demo en vivo')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url($this->previewUrl, shouldOpenInNewTab: true) : null,
            Action::make('save')
                ->label('Guardar cambios')
                ->icon('heroicon-o-check')
                ->action('save'),
        ]);
    }
}
