<?php

namespace App\Filament\Pages;

use App\Models\Catalogue;
use App\Models\Category;
use App\Models\Product;
use App\Services\CatalogueBuilder;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Turns the live catalogue into the PDF the sales desk sends out.
 *
 * The products, their pack sizes and their prices are already in here, kept up
 * to date because the website prints them — so the price list should come from
 * the same place rather than being a Word file somebody has to remember to
 * update twice.
 *
 * The admin chooses what goes in and what the cover says; "Save as the site's
 * master catalogue" puts the result straight behind the storefront's Download
 * buttons, which is the whole loop without leaving the panel.
 *
 * @property-read Schema $form
 */
class ProductCataloguePage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Catalogue PDF Builder';

    protected static ?int $navigationSort = 22;

    protected static ?string $title = 'Catalogue PDF Builder';

    protected static ?string $slug = 'catalogue-builder';

    protected string $view = 'filament.pages.product-catalogue';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'segment' => 'all',
            'title' => 'Product Catalogue & Price List',
            'subtitle' => 'Groceries, FMCG, housekeeping and industrial supplies — stocked for homes, retailers and businesses across Surat.',
            'show_prices' => true,
            'show_descriptions' => true,
            'show_images' => true,
            'show_sku' => false,
            'include_hidden' => false,
            'save_as_master' => false,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('What goes in')
                    ->schema([
                        Radio::make('segment')
                            ->label('Which side of the business')
                            ->options([
                                'all' => 'Everything — the full range',
                                Category::TYPE_STORE => 'Store / retail products only',
                                Category::TYPE_B2B => 'B2B / corporate supply only',
                            ])
                            ->default('all')
                            ->live()
                            ->columnSpanFull(),

                        Select::make('categories')
                            ->label('Categories')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (Get $get) => Category::query()
                                ->where('is_active', true)
                                ->when($get('segment') !== 'all', fn ($query) => $query->where('type', $get('segment')))
                                ->orderBy('type')
                                ->orderBy('sort_order')
                                ->pluck('name', 'id')
                                ->all())
                            ->helperText('Leave empty for every category on the chosen side.')
                            ->columnSpanFull(),

                        Checkbox::make('show_prices')
                            ->label('Print prices')
                            ->helperText('Off for a range-only catalogue with no rates on it.'),

                        Checkbox::make('show_descriptions')
                            ->label('Print the short description'),

                        Checkbox::make('show_images')
                            ->label('Print product photos')
                            ->helperText('A small thumbnail beside each product.'),

                        Checkbox::make('show_sku')
                            ->label('Print product codes')
                            ->helperText('Useful for an order sheet; usually off for a customer copy.'),

                        Checkbox::make('include_hidden')
                            ->label('Include products hidden on the website')
                            ->helperText('Off by default — the PDF shows what the site shows.'),
                    ])
                    ->columns(2),

                Section::make('The cover')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(120)
                            ->columnSpanFull(),

                        Textarea::make('subtitle')
                            ->label('Line under the title')
                            ->rows(2)
                            ->maxLength(400)
                            ->columnSpanFull(),
                    ]),

                Section::make('After it is made')
                    ->schema([
                        Checkbox::make('save_as_master')
                            ->label('Also save it as the site\'s master catalogue')
                            ->helperText('The "Download Now" buttons on the site start serving this file. Retail or B2B is decided by the side chosen above; the full range is filed under Retail.')
                            ->live()
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * The whole point of the page: the file itself.
     */
    public function download(): ?StreamedResponse
    {
        $options = $this->form->getState();

        // Raised before building the data, which is where the photos are
        // shrunk — the slow part of a first run.
        $this->raiseLimits();

        // Leaves the rest of the gateway's ~60 seconds for typesetting.
        $builder = app(CatalogueBuilder::class)->limitThumbnailTime(25);
        $data = $builder->data($options);

        if ($data['productCount'] === 0) {
            Notification::make()
                ->warning()
                ->title('Nothing to print')
                ->body('No products matched that selection. Widen the categories, or check that the products are live.')
                ->send();

            // Nothing returned: the click leaves the page as it is, with the
            // notice explaining why no file arrived.
            return null;
        }

        // Says so plainly rather than dying inside the container: this page is
        // the only thing on the site that needs dompdf, so a server that
        // pulled the code without running composer install would otherwise
        // answer the click with a bare 500.
        if (! class_exists(Pdf::class)) {
            Notification::make()
                ->danger()
                ->title('The PDF library is not installed on this server')
                ->body('Run "composer install" on the server and try again — the catalogue needs barryvdh/laravel-dompdf.')
                ->persistent()
                ->send();

            return null;
        }

        try {
            $pdf = $builder->pdf($options, $data);
            $output = $pdf->output();
        } catch (\Throwable $e) {
            // Without this the click comes back as a bare 500 in the console
            // and the page just sits there.
            report($e);

            Notification::make()
                ->danger()
                ->title('The catalogue could not be built')
                ->body(Str::limit($e->getMessage(), 300))
                ->persistent()
                ->send();

            return null;
        }

        $filename = $builder->filename($options);

        if ($options['save_as_master'] ?? false) {
            $this->saveAsMaster($output, $options, $filename);
        }

        $body = $data['productCount'].' products across '.$data['categoryCount'].' categories.';

        if ($data['photosPending'] > 0) {
            Notification::make()
                ->warning()
                ->title('Catalogue ready — some photos still being prepared')
                ->body($body.' '.$data['photosPending'].' products printed without a photo this time. Make the PDF again to fill them in.')
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->success()
                ->title('Catalogue ready')
                ->body($body)
                ->send();
        }

        return response()->streamDownload(fn () => print $output, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Give this one request the headroom a long catalogue needs.
     *
     * Typesetting a whole catalogue is heavier than an ordinary page load:
     * a 440-product run peaks around 120 MB, which is the default limit on
     * a lot of shared hosting. Raised for this request only, and only
     * upwards — a host that already allows more keeps its own setting.
     *
     * Both are no-ops where the host forbids them (`ini_set` disabled, or PHP
     * running in safe mode), which is why neither return value is trusted.
     */
    private function raiseLimits(): void
    {
        $current = trim((string) ini_get('memory_limit'));

        // "-1" is unlimited; anything below 512M is raised to it.
        if ($current !== '-1' && $this->bytes($current) < 512 * 1024 * 1024) {
            @ini_set('memory_limit', '512M');
        }

        if ((int) ini_get('max_execution_time') !== 0) {
            @set_time_limit(300);
        }
    }

    /** "256M" -> bytes, for comparing against a limit. */
    private function bytes(string $value): int
    {
        $number = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }

    /**
     * File the generated PDF behind the storefront's download buttons.
     *
     * Stored under its own name each time rather than overwriting, so the
     * previous edition is still on disk if the new one has to be pulled.
     *
     * @param  array<string, mixed>  $options
     */
    private function saveAsMaster(string $contents, array $options, string $filename): void
    {
        $type = ($options['segment'] ?? 'all') === Category::TYPE_B2B
            ? Catalogue::TYPE_B2B
            : Catalogue::TYPE_RETAIL;

        $path = 'catalogues/'.$filename;

        Storage::disk('public')->put($path, $contents);

        $catalogue = Catalogue::where('type', $type)->orderBy('sort_order')->first() ?? new Catalogue(['type' => $type]);

        $catalogue->fill([
            'type' => $type,
            'title' => $options['title'] ?: Catalogue::TYPES[$type],
            'description' => $options['subtitle'] ?: $catalogue->description,
            'file' => $path,
            'is_active' => true,
        ])->save();
    }

    /**
     * The counts printed above the button, so the admin knows what they are
     * about to get before they get it.
     *
     * @return array<string, int>
     */
    public function getPreview(): array
    {
        $options = $this->form->getRawState();

        $data = app(CatalogueBuilder::class)->data([
            'segment' => $options['segment'] ?? 'all',
            'categories' => $options['categories'] ?? [],
            'include_hidden' => $options['include_hidden'] ?? false,
            // Only counts are shown here; skip the photo work entirely.
            'show_images' => false,
        ]);

        return [
            'products' => $data['productCount'],
            'categories' => $data['categoryCount'],
            'total' => Product::active()->count(),
        ];
    }
}
