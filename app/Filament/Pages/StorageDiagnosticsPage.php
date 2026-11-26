<?php

namespace App\Filament\Pages;

use App\Services\StorageLink;
use App\Support\UploadDebug;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class StorageDiagnosticsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Storage Diagnostics';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Storage Diagnostics';

    protected static ?string $slug = 'storage-diagnostics';

    protected string $view = 'filament.pages.storage-diagnostics';

    public array $status = [];

    public string $log = '';

    public function mount(): void
    {
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $this->status = app(StorageLink::class)->status();
        $this->log = UploadDebug::tail();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('repair')
                ->label('Repair Storage Link')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Repair the storage link?')
                ->modalDescription('This points public/storage at storage/app/public, the same thing `php artisan storage:link` does. If a real folder is sitting in the way it is renamed to storage-stale-<date> rather than deleted, so nothing is lost.')
                ->modalSubmitActionLabel('Yes, repair it')
                ->action(function () {
                    $result = app(StorageLink::class)->repair();

                    $this->refreshStatus();

                    $notification = Notification::make()
                        ->title($result['ok'] ? 'Storage link repaired' : 'Repair failed')
                        ->body($result['message'].($result['moved_aside'] ? ' The old folder was moved to '.basename($result['moved_aside']).'.' : ''));

                    $result['ok'] ? $notification->success() : $notification->danger();

                    $notification->persistent()->send();
                }),

            Action::make('refresh')
                ->label('Re-check')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->refreshStatus()),

            Action::make('clearLog')
                ->label('Clear Log')
                ->icon('heroicon-o-trash')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    UploadDebug::clear();
                    $this->refreshStatus();

                    Notification::make()->title('Upload log cleared')->success()->send();
                }),
        ];
    }
}
