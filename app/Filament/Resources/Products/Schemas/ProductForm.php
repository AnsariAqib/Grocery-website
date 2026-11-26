<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product')
                ->schema([
                    // Not a product column: this only narrows the subcategory
                    // list, so it is hydrated from the saved record and dropped
                    // again before saving.
                    Select::make('category_id')
                        ->label('Category')
                        ->options(fn () => Category::query()
                            ->orderBy('type')
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(fn (Category $c) => [$c->id => $c->name])
                            ->all())
                        ->live()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn (Select $component, ?object $record) => $component->state($record?->subcategory?->category_id))
                        ->afterStateUpdated(fn (Set $set) => $set('subcategory_id', null))
                        ->searchable()
                        ->required(),

                    Select::make('subcategory_id')
                        ->label('Subcategory')
                        ->options(fn (Get $get) => Subcategory::query()
                            ->when($get('category_id'), fn ($q, $id) => $q->where('category_id', $id))
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->required()
                        ->helperText('Pick a category first — this list follows it.'),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state, ?object $record) {
                            // Only for new records: changing a live slug would
                            // break links already shared.
                            if ($record === null) {
                                $set('slug', Str::slug((string) $state));
                            }
                        })
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Filled in from the name.'),

                    TextInput::make('sku')
                        ->label('SKU')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('The supplier\'s own code. A bulk import matches on this to update the product instead of creating a second copy.'),

                    TextInput::make('brand')
                        ->maxLength(255)
                        ->placeholder('Amul / Mother Dairy / Hocco')
                        ->helperText('Several brands go in with a "/" between them — "Amul / Mother Dairy / Hocco". The catalogue then lists each one separately in the Brand filter, and picking any of them finds this product.'),

                    TextInput::make('pack_size')
                        ->label('Pack size')
                        ->maxLength(255)
                        ->placeholder('5 Litre Can')
                        ->helperText('Only used when there are no pack sizes listed below.'),

                    Textarea::make('description')
                        ->label('Short description')
                        ->maxLength(255)
                        ->rows(2)
                        ->placeholder('Cold-pressed groundnut oil, sealed at the mill.')
                        ->helperText('One line under the product name on the catalogue card. Keep it under ~90 characters.')
                        ->columnSpanFull(),

                    Textarea::make('search_keywords')
                        ->label('Search keywords')
                        ->rows(2)
                        ->placeholder('phenyl, floor cleaner, disinfectant')
                        ->helperText('Extra words the catalogue search should find this product by. Never shown on the card.')
                        ->columnSpanFull(),

                    Toggle::make('price_on_request')
                        ->label('Price on request')
                        ->helperText('Hides the price on the storefront and prints "Price on Request" instead.')
                        ->live()
                        ->default(false),

                    TextInput::make('price')
                        ->maxLength(255)
                        ->placeholder('₹675.00')
                        // Kept in the record either way, so switching back does
                        // not cost the admin the price they already typed.
                        ->disabled(fn (Get $get) => (bool) $get('price_on_request'))
                        ->dehydrated()
                        ->helperText(fn (Get $get) => $get('price_on_request')
                            ? 'Not shown while "Price on request" is on.'
                            : 'Shown exactly as typed, including the ₹ symbol.'),
                ])
                ->columns(2),

            Section::make('Photos')
                ->description('Up to three photos. The visitor flips through them with the dots on the card, and the first one is the product\'s face everywhere else on the site.')
                ->schema([
                    FileUpload::make('images')
                        ->label('Product photos')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->maxFiles(Product::MAX_IMAGES)
                        ->disk('public')
                        ->directory('products')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('Cut-out product images on a transparent or white background. Drag to reorder.')
                        ->columnSpanFull(),
                ]),

            Section::make('Pack sizes')
                ->description('One row per size. The card shows them as chips, and picking one switches the price and the photos. Leave this empty for a product that comes in a single size.')
                ->schema([
                    Repeater::make('variants')
                        ->hiddenLabel()
                        ->addActionLabel('Add a pack size')
                        ->reorderable()
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['size'] ?? null)
                        ->defaultItems(0)
                        ->schema([
                            TextInput::make('size')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('500 ml'),

                            TextInput::make('sku')
                                ->label('SKU')
                                ->maxLength(255),

                            Toggle::make('price_on_request')
                                ->label('Price on request')
                                ->live()
                                ->default(false),

                            TextInput::make('price')
                                ->maxLength(255)
                                ->placeholder('₹193')
                                ->disabled(fn (Get $get) => (bool) $get('price_on_request'))
                                ->dehydrated(),

                            FileUpload::make('images')
                                ->label('Photos for this size')
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->maxFiles(Product::MAX_IMAGES)
                                ->disk('public')
                                ->directory('products')
                                ->maxSize(25600)
                                ->helperText('Leave empty to use the product photos above.')
                                ->columnSpanFull(),

                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->helperText('Turn off to hide just this size.'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Card appearance')
                ->schema([
                    TextInput::make('badge')
                        ->maxLength(255)
                        ->placeholder('SUPERMARKET BESTSELLER'),

                    ColorPicker::make('badge_bg')
                        ->label('Badge colour')
                        ->helperText('Hex colour for the badge background.'),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers come first.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Toggle::make('is_featured')
                        ->label('Featured on the home page')
                        ->helperText('Shows in the Featured Products rail (first 8).')
                        ->default(false),
                ])
                ->columns(2),
        ]);
    }
}
