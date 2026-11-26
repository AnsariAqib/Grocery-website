<?php

namespace App\Filament\Pages;

use App\Services\ImageCompressor;
use App\Services\ProductImageLibrary;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Drops a whole folder of product photos onto the site in one go.
 *
 * The import sheet names its photos rather than carrying them, so the photos
 * have to be on the disk before a sheet can be imported. Uploading them one
 * product at a time is not realistic for a few hundred rows, and the person
 * doing it has admin access only — no FTP, no shell.
 *
 * @property-read Schema $form
 */
class ProductImageUploadPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Bulk Image Upload';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Bulk Image Upload';

    protected static ?string $slug = 'bulk-image-upload';

    protected string $view = 'filament.pages.product-image-upload';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public int $lastBatch = 0;

    public int $bytesSaved = 0;

    /** How many photos the gallery is currently showing. */
    public int $shown = self::PER_PAGE;

    private const PER_PAGE = 10;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('images')
                    ->label('Product photos')
                    ->image()
                    ->multiple()
                    ->panelLayout('grid')
                    ->disk('public')
                    ->directory(ProductImageLibrary::DIRECTORY)
                    ->maxSize(25600)
                    // Photos are matched to the sheet by name, so the name the
                    // file arrives with is the name it has to keep — tidied to
                    // something a Linux host will serve.
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file): string => ProductImageLibrary::normalise($file->getClientOriginalName()),
                    )
                    ->helperText('Drop as many as you like at once. Each keeps its own filename — that is what the import sheet points at.'),
            ])
            ->statePath('data');
    }

    /**
     * The photos are already on the disk — dropping them stored them. This
     * squeezes the batch down and empties the field for the next one.
     */
    public function finish(): void
    {
        $paths = array_filter(Arr::wrap($this->form->getState()['images'] ?? []));

        if ($paths === []) {
            Notification::make()
                ->title('Nothing to do')
                ->body('Drop some photos in first.')
                ->info()
                ->send();

            return;
        }

        $compressor = app(ImageCompressor::class);
        $saved = 0;

        foreach ($paths as $path) {
            $saved += $compressor->compressFile($path);
        }

        $this->lastBatch = count($paths);
        $this->bytesSaved = $saved;

        // Clearing the field only clears the form; the files stay on the disk.
        $this->form->fill();

        Notification::make()
            ->title($this->lastBatch.' '.str('photo')->plural($this->lastBatch).' uploaded')
            ->body($saved > 0 ? 'Compressed, saving '.$this->formatBytes($saved).'.' : 'Already small enough to leave alone.')
            ->success()
            ->send();
    }

    public function imageCount(): int
    {
        return ProductImageLibrary::count();
    }

    /**
     * The gallery, newest first. Only what is on screen is loaded — a full
     * catalogue's worth of photos at once would be a very heavy page.
     *
     * @return array<int, array{name: string, url: string}>
     */
    public function recentImages(): array
    {
        return ProductImageLibrary::page($this->shown);
    }

    public function hasMoreImages(): bool
    {
        return $this->imageCount() > $this->shown;
    }

    public function loadMore(): void
    {
        $this->shown += self::PER_PAGE;
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
