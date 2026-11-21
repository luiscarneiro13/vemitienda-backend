<?php

namespace App\Filament\Pages;

use App\Core\Settings\SettingsManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Configuración';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(SettingsManager::class);

        $this->form->fill([
            'app_name' => $settings->get('app_name', config('app.name')),
            'support_email' => $settings->get('support_email'),
            'timezone' => $settings->get('timezone', config('app.timezone')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Parámetros Generales de la Plataforma')
                    ->description('Configuración global del sistema ChatPedido.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('app_name')
                            ->label('Nombre de la aplicación')
                            ->required(),
                        TextInput::make('support_email')
                            ->label('Correo de soporte')
                            ->email(),
                        TextInput::make('timezone')
                            ->label('Zona horaria')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = app(SettingsManager::class);

        foreach ($data as $key => $value) {
            $settings->set($key, $value, 'string', 'general');
        }

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar')
                ->action('save'),
        ];
    }
}
