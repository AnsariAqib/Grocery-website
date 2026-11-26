<?php

namespace App\Filament\Pages;

use App\Services\ProductCsvExporter;
use App\Services\ProductImporter;
use App\Services\ProductImportResult;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The panel face of {@see ProductImporter}.
 *
 * The importer began as a shell command, but the person who runs it has admin
 * access only — no FTP, no shell — the same reason the Bulk Image Upload page
 * exists. Both halves of the job now live in the panel.
 *
 * A sheet is always walked twice: once as a dry run, whose report is shown for
 * approval, then once for real. That makes the risky part — new categories,
 * photos that were never uploaded — visible before anything is written.
 *
 * @property-read Schema $form
 */
class ProductImportPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Bulk Product Import';

    protected static ?int $navigationSort = 21;

    protected static ?string $title = 'Bulk Product Import';

    protected static ?string $slug = 'bulk-product-import';

    protected string $view = 'filament.pages.product-import';

    /** @var array<string, mixed> */
    public ?array $data = [];

    /**
     * The last run, as {@see ProductImportResult::toArray()}.
     *
     * @var array<string, mixed>
     */
    public array $result = [];

    /** False while `$result` is only a preview, true once it has been written. */
    public bool $committed = false;

    /** Our own copy of the uploaded sheet — see {@see self::keep()}. */
    public ?string $sheetPath = null;

    public ?string $sheetName = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // The other half of a round trip: pull the catalogue out in this
            // page's own format, edit it, feed it back. Existing products are
            // matched on SKU and updated rather than duplicated.
            Action::make('export')
                ->label('Export catalogue')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => app(ProductCsvExporter::class)->download()),

            Action::make('template')
                ->label('Download template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn (): StreamedResponse => response()->streamDownload(
                    fn () => print (ProductImporter::sampleCsv()),
                    'jay-tulsi-product-import-template.csv',
                    ['Content-Type' => 'text/csv'],
                )),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('sheet')
                    ->label('Catalogue sheet')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                    ->maxSize(20480)
                    // Not stored through a disk: the only one configured here
                    // is `public`, and a price list has no business being
                    // web-readable. Livewire's own temporary file is no good
                    // either — it is replaced on every request the component
                    // makes, so by the time "Import now" is pressed the path
                    // held in state points at a file that is gone. The sheet
                    // is copied somewhere of our own the moment it lands.
                    ->storeFiles(false)
                    // Named `$state` because that is one of the arguments
                    // Filament injects by name; an unrecognised name is looked
                    // up in the container by type instead, and a temporary file
                    // is not something the container can build.
                    ->afterStateUpdated(fn ($state) => $this->keep(Arr::first(Arr::wrap($state))))
                    ->helperText('A .csv export. Save an Excel sheet as CSV first — .xlsx will not be read.'),
            ])
            ->statePath('data');
    }

    /**
     * Walk the sheet, report what it would do, write nothing.
     */
    public function checkSheet(): void
    {
        $this->run(dryRun: true);
    }

    /**
     * Walk it again and keep the changes.
     */
    public function startImport(): void
    {
        $this->run(dryRun: false);
    }

    /**
     * Take our own copy of a freshly uploaded sheet, so neither reading it nor
     * importing it depends on Livewire's temporary file still being there.
     *
     * Plain filesystem calls rather than a disk: `storage/app/imports` is not
     * one of the configured disks, and going through Flysystem would drag in
     * the root-creation it does on a disk it has never written to.
     */
    private function keep(mixed $file): void
    {
        $this->discard();

        // Clearing the field lands here too, with nothing in state.
        if (! $file instanceof TemporaryUploadedFile) {
            return;
        }

        $directory = storage_path('app/imports');

        if (! is_dir($directory) && ! @mkdir($directory, 0775, true) && ! is_dir($directory)) {
            Notification::make()
                ->title('Could not hold on to the sheet')
                ->body('storage/app/imports is not writable. Ask whoever runs the server to create it.')
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $this->sweep($directory);

        $path = $directory.DIRECTORY_SEPARATOR.Str::uuid()->toString().'.csv';

        if (! @copy($file->getRealPath(), $path)) {
            Notification::make()
                ->title('Could not read the sheet')
                ->body('The upload did not arrive in one piece. Try again.')
                ->danger()
                ->send();

            return;
        }

        $this->sheetPath = $path;
        $this->sheetName = $file->getClientOriginalName();
        $this->result = [];
        $this->committed = false;
    }

    /** Our copy is a scratch file; once it is done with, it goes. */
    private function discard(): void
    {
        if ($this->sheetPath !== null && is_file($this->sheetPath)) {
            @unlink($this->sheetPath);
        }

        $this->sheetPath = null;
        $this->sheetName = null;
    }

    /**
     * Copies from abandoned sessions — the admin closed the tab mid-import —
     * would otherwise pile up unnoticed.
     */
    private function sweep(string $directory): void
    {
        foreach (glob($directory.DIRECTORY_SEPARATOR.'*.csv') ?: [] as $stale) {
            if (filemtime($stale) < now()->subDay()->getTimestamp()) {
                @unlink($stale);
            }
        }
    }

    private function run(bool $dryRun): void
    {
        // Normally the copy was taken the moment the sheet landed. If it was
        // not — the upload finished without the hook running — the file is
        // still in the form, so take it now rather than refusing.
        if ($this->sheetPath === null || ! is_file($this->sheetPath)) {
            $this->keep(Arr::first(Arr::wrap($this->form->getRawState()['sheet'] ?? [])));
        }

        if ($this->sheetPath === null || ! is_file($this->sheetPath)) {
            Notification::make()
                ->title('No sheet yet')
                ->body('Choose a CSV file first, then press "Import now".')
                ->info()
                ->send();

            return;
        }

        // A full catalogue is more than the default 30 seconds allows, and the
        // import runs inside one transaction — being cut off half way would
        // roll the whole sheet back.
        @set_time_limit(0);

        try {
            $result = app(ProductImporter::class)->import($this->sheetPath, $dryRun);
        } catch (\Throwable $e) {
            $this->result = [];
            $this->committed = false;

            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage().' Nothing was written.')
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $this->result = $result->toArray();
        $this->committed = ! $dryRun;

        if ($dryRun) {
            Notification::make()
                ->title('Checked '.$result->rows.' rows')
                ->body($result->hasProblems()
                    ? 'Have a look at the warnings below before importing.'
                    : 'Everything looks clean. Import when ready.')
                ->info()
                ->send();

            return;
        }

        // Clearing the field stops a second click importing the same sheet
        // twice — harmless, since products match on SKU, but confusing.
        $this->discard();
        $this->form->fill();

        Notification::make()
            ->title($result->productsTouched().' products imported')
            ->body($result->productsCreated.' new, '.$result->productsUpdated.' updated.')
            ->success()
            ->send();
    }
}
