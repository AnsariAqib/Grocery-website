<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\ImageCompressor;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ImageCompressorPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Image Compressor';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Image Compressor';

    protected static ?string $slug = 'image-compressor';

    protected string $view = 'filament.pages.image-compressor';

    public bool $compressionEnabled = true;

    public int $quality = 82;

    public bool $hasRun = false;

    public int $compressed = 0;

    public int $skipped = 0;

    public int $bytesSaved = 0;

    public function mount(): void
    {
        $this->compressionEnabled = Setting::bool('image_compression_enabled', true);
        $this->quality = (int) Setting::val('image_compression_quality', 82);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('compressAll')
                ->label('Compress All Images')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Compress All Uploaded Images?')
                ->modalDescription('This will scan every upload folder and re-encode images at the configured quality. Original files will be overwritten with compressed versions. Files that are already small will be skipped.')
                ->modalSubmitActionLabel('Yes, Compress Now')
                ->action(function () {
                    $compressor = app(ImageCompressor::class);
                    $stats = $compressor->compressBulk();

                    $this->hasRun = true;
                    $this->compressed = $stats['compressed'];
                    $this->skipped = $stats['skipped'];
                    $this->bytesSaved = $stats['bytes_saved'];

                    if ($stats['compressed'] > 0) {
                        Notification::make()
                            ->title('Compression Complete')
                            ->body("Compressed {$stats['compressed']} images, saved ".$this->formatBytes($stats['bytes_saved']))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No Images to Compress')
                            ->body('All images are already optimised or compression is disabled.')
                            ->info()
                            ->send();
                    }
                }),
        ];
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
