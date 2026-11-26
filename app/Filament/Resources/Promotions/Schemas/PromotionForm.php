<?php

namespace App\Filament\Resources\Promotions\Schemas;

use App\Models\Promotion;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Where it appears')
                ->description('Pick the placements first — the fields below are labelled for whichever you tick.')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Toggle::make('show_on_home')
                        ->label('Home page')
                        ->helperText('The four-card promotions row on the home page.')
                        ->live(),

                    Toggle::make('show_on_page')
                        ->label('Offers page')
                        ->helperText('The /promotions catalogue, grouped by category.')
                        ->default(true)
                        ->live(),

                    Select::make('group')
                        ->label('Offers page group')
                        ->options(Promotion::GROUPS)
                        ->default('latest')
                        ->native(false)
                        ->required(fn (Get $get) => (bool) $get('show_on_page'))
                        ->visible(fn (Get $get) => (bool) $get('show_on_page'))
                        ->helperText('Which heading it sits under on the offers page.'),

                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Lower shows first. Ties fall back to newest.'),

                    // Both toggles off is a valid draft state, but it is almost
                    // always a mistake, so say so instead of silently saving a
                    // promotion that renders nowhere.
                    Text::make('Both placements are off — this promotion will not appear anywhere on the site.')
                        ->badge()
                        ->color('warning')
                        ->visible(fn (Get $get) => ! $get('show_on_home') && ! $get('show_on_page'))
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Offer content')
                ->description('The badge, headline and title are what a customer actually reads on the card.')
                ->icon('heroicon-o-tag')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Supermarket Ration Package')
                        ->helperText('The big line on the card.')
                        ->columnSpanFull(),

                    TextInput::make('badge')
                        ->label('Badge')
                        ->maxLength(255)
                        ->placeholder('WEEKLY SAVINGS')
                        ->helperText('Small pill above the title. Short and uppercase reads best.'),

                    TextInput::make('discount')
                        ->label('Headline offer')
                        ->maxLength(255)
                        ->placeholder('UP TO 20% OFF')
                        ->helperText('The saving, in large coloured type.'),

                    TextInput::make('subtitle')
                        ->label('Subtitle — offers page')
                        ->maxLength(255)
                        ->placeholder('On monthly grocery combos above ₹2,999')
                        ->helperText('One supporting line under the headline. Only the offers page shows this.')
                        ->visible(fn (Get $get) => (bool) $get('show_on_page'))
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Description — home page')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Flat 15% OFF on monthly grocery combos above ₹2,999.')
                        ->helperText('The body text on the home page card. Only the home page shows this.')
                        ->visible(fn (Get $get) => (bool) $get('show_on_home'))
                        ->columnSpanFull(),

                    TextInput::make('cta_text')
                        ->label('Button text')
                        ->maxLength(40)
                        ->default('Claim Deal')
                        ->placeholder('Claim Deal')
                        ->helperText('Two or three words.'),

                    TextInput::make('cta_url')
                        ->label('Button link')
                        ->maxLength(255)
                        ->placeholder('/products?category=Grocery')
                        ->helperText('Where the button goes. A path on this site (/products, /promotions) or a full https:// address. Leave blank and it opens the enquiry form, as it does today.'),

                    TextInput::make('validity')
                        ->label('Validity text')
                        ->maxLength(255)
                        ->placeholder('Valid till stocks last')
                        ->helperText('Free text next to the calendar icon. Only used when no expiry date is set below — an expiry date prints itself.'),

                    FileUpload::make('image')
                        ->label('Product image')
                        ->image()
                        ->disk('public')
                        ->directory('promotions')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('A cut-out product shot on a transparent PNG sits best on the card. Uploads are compressed automatically.')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Schedule')
                ->description('Times are '.config('app.timezone').' — the same clock as the store. Leave both blank and the offer simply runs while "Active" is on.')
                ->icon('heroicon-o-clock')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('The master switch. Off hides the promotion no matter what the dates say.')
                        ->live()
                        ->columnSpanFull(),

                    DateTimePicker::make('publish_at')
                        ->label('Goes live')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d M Y, H:i')
                        ->helperText('Blank = live as soon as it is active.')
                        ->live(onBlur: true)
                        ->beforeOrEqual('expire_at')
                        ->validationMessages([
                            'before_or_equal' => 'The start time has to be before the expiry time.',
                        ]),

                    DateTimePicker::make('expire_at')
                        ->label('Expires')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d M Y, H:i')
                        ->helperText('Blank = runs until you switch it off. At this moment the card disappears from the site on its own.')
                        ->live(onBlur: true)
                        ->afterOrEqual('publish_at')
                        ->validationMessages([
                            'after_or_equal' => 'The expiry time has to be after the start time.',
                        ]),

                    // The single thing the old form was missing: what the dates
                    // you just typed actually mean right now.
                    Text::make(fn (Get $get) => self::statusLine($get))
                        ->badge()
                        ->color(fn (Get $get) => self::statusColour($get))
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Card colours')
                ->description('Optional. Left empty, the card falls back to the site defaults; the home card always takes its colour from the badge.')
                ->icon('heroicon-o-swatch')
                ->schema([
                    ColorPicker::make('badge_bg')
                        ->label('Badge colour'),

                    ColorPicker::make('discount_color')
                        ->label('Headline offer colour')
                        ->helperText('Falls back to the badge colour when empty.'),

                    ColorPicker::make('border_color')
                        ->label('Card border')
                        ->visible(fn (Get $get) => (bool) $get('show_on_page')),

                    Toggle::make('is_dark')
                        ->label('Navy card treatment')
                        ->helperText('Light text on a dark card. Pair it with a dark background below.')
                        ->visible(fn (Get $get) => (bool) $get('show_on_page')),

                    TextInput::make('card_bg')
                        ->label('Card background')
                        ->maxLength(255)
                        ->placeholder('linear-gradient(135deg, #FEF7F3 0%, #FFEFE6 100%)')
                        ->helperText('Any CSS background value — a plain #hex or a gradient both work. Offers page only.')
                        ->visible(fn (Get $get) => (bool) $get('show_on_page'))
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }

    /**
     * Plain-English reading of the three schedule fields together, so the admin
     * does not have to work out whether what they typed is currently showing.
     */
    private static function statusLine(Get $get): string
    {
        if (! $get('is_active')) {
            return 'Hidden — "Active" is off, so this is not on the site.';
        }

        $publish = self::asDate($get('publish_at'));
        $expire = self::asDate($get('expire_at'));

        if ($expire && $expire->isPast()) {
            return 'Expired '.$expire->diffForHumans().' — already off the site.';
        }

        if ($publish && $publish->isFuture()) {
            return 'Scheduled — goes live '.$publish->diffForHumans().($expire ? ', then expires '.$expire->diffForHumans() : '.');
        }

        return 'Live now'.($expire ? ' — expires '.$expire->diffForHumans().'.' : ' — no expiry set.');
    }

    private static function statusColour(Get $get): string
    {
        if (! $get('is_active')) {
            return 'gray';
        }

        $expire = self::asDate($get('expire_at'));
        if ($expire && $expire->isPast()) {
            return 'danger';
        }

        $publish = self::asDate($get('publish_at'));
        if ($publish && $publish->isFuture()) {
            return 'warning';
        }

        return 'success';
    }

    /** The picker hands back a string while typing and a Carbon once saved. */
    private static function asDate(mixed $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
