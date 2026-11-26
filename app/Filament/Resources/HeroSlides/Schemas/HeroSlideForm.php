<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    /**
     * The places a hero button usually goes, offered by name so nobody has to
     * remember that the B2B panel is "#b2b".
     */
    private const PRESETS = [
        '#categories' => 'Home: Store Categories Section',
        '#b2b' => 'Home: B2B Commercial Section',
        '#promotions' => 'Home: Promotions Section',
        '#home' => 'Home: Top Section',
        '/products' => 'Page: All Products',
        '/categories' => 'Page: All Categories',
        '/b2b-categories' => 'Page: B2B Categories',
        '/stores' => 'Page: Store Locations',
        '/promotions' => 'Page: All Promotions',
        '/about' => 'Page: About Us',
        '/contact' => 'Page: Contact Us',
    ];

    /** What the list shows when the box holds an address of its own. */
    private const CUSTOM = '__custom__';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Slide')
                ->schema([
                    FileUpload::make('image')
                        ->label('Background image')
                        ->image()
                        ->disk('public')
                        ->directory('hero')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('Wide photo, around 1400px across.')
                        ->columnSpanFull(),

                    Textarea::make('title_html')
                        ->label('Headline')
                        ->required()
                        ->rows(3)
                        ->helperText('Surround a word with *asterisks* to make it an orange gradient (e.g. We sell *quality* products). Press Enter for line breaks.')
                        ->columnSpanFull(),

                    Textarea::make('subtext')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Buttons')
                ->schema([
                    Fieldset::make('Orange Button')
                        ->schema([
                            TextInput::make('btn_primary_text')
                                ->label('Button text')
                                ->maxLength(255),

                            Toggle::make('btn_primary_has_arrow')
                                ->label('Show arrow icon')
                                ->default(true),

                            ...self::linkFields('btn_primary_link'),
                        ])
                        ->columns(1)
                        ->columnSpan(1),

                    Fieldset::make('Navy Button')
                        ->schema([
                            TextInput::make('btn_navy_text')
                                ->label('Button text')
                                ->maxLength(255),

                            Toggle::make('btn_navy_has_arrow')
                                ->label('Show arrow icon')
                                ->default(true),

                            ...self::linkFields('btn_navy_link'),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make()
                ->schema([
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Slide order in the rotation.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    /**
     * Where a button goes: one of the usual places, or anywhere at all.
     *
     * Two controls over one column, and only the second is saved. The list
     * carries the plain-English labels for the everyday destinations and writes
     * the one that is picked into the box; the box takes anything at all — a
     * page, a single product, a section anchor, or another site entirely.
     *
     * @return array<int, Field>
     */
    private static function linkFields(string $field): array
    {
        $chooser = $field.'_choice';

        return [
            Select::make($chooser)
                ->label('Quick pick')
                ->options(self::PRESETS + [self::CUSTOM => 'Custom link (typed below)'])
                ->searchable()
                // Not a column on the table — it only decides what goes in one.
                ->dehydrated(false)
                ->live()
                // An existing slide opens on the entry that matches what it is
                // already pointing at; anything else is a custom link.
                ->afterStateHydrated(function (Select $component, Get $get) use ($field) {
                    $saved = (string) $get($field);

                    $component->state(match (true) {
                        $saved === '' => null,
                        array_key_exists($saved, self::PRESETS) => $saved,
                        default => self::CUSTOM,
                    });
                })
                // Choosing from the list writes straight through to the column,
                // so the two never disagree.
                ->afterStateUpdated(function (?string $state, Set $set) use ($field) {
                    if ($state !== null && $state !== self::CUSTOM) {
                        $set($field, $state);
                    }
                })
                ->helperText('Fills the box below with one of the usual places. Or leave this alone and type any address yourself.'),

            // Always on screen, never hidden behind the list. Hiding it was the
            // obvious design and the wrong one: the field the list writes into
            // is exactly the field that would be hidden at that moment, and a
            // hidden field's state does not survive the save, so switching from
            // a custom link back to a preset kept saving the old address.
            // Visible, it is also simply honest — the box shows what will be
            // stored, whichever way it was filled in.
            TextInput::make($field)
                ->label('Link')
                ->maxLength(255)
                ->placeholder('/products/basmati-rice-premium-long-grain')
                ->helperText('Set by the list above, or type your own: a page on this site (/products, /contact), one product, a section (#b2b), or a full https:// address somewhere else — which opens in a new tab.'),
        ];
    }
}
