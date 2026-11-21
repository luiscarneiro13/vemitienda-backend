<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LogViewer extends Page
{
    protected string $view = 'filament.pages.log-viewer';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Logs';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public string $logContent = '';

    private const MAX_LINES = 500;

    public function mount(): void
    {
        $this->loadLog();
    }

    public function loadLog(): void
    {
        $path = storage_path('logs/laravel.log');

        if (! file_exists($path)) {
            $this->logContent = 'No hay archivo de log todavía.';

            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $tail = array_slice($lines, -self::MAX_LINES);

        $this->logContent = implode("\n", $tail);
    }

    public function clearLog(): void
    {
        $path = storage_path('logs/laravel.log');

        if (file_exists($path)) {
            file_put_contents($path, '');
        }

        $this->loadLog();

        Notification::make()
            ->title('Log limpiado')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Actualizar')
                ->icon('heroicon-o-arrow-path')
                ->action('loadLog'),
            Action::make('clear')
                ->label('Limpiar log')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action('clearLog'),
        ];
    }
}
