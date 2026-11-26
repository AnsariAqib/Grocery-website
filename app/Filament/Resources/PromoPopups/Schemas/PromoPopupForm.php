<?php

namespace App\Filament\Resources\PromoPopups\Schemas;

use App\Models\PromoPopup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class PromoPopupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('What the popup says')
                ->description('Keep it to one offer and one action — a popup that has to be read twice gets closed.')
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->schema([
                    TextInput::make('title')
                        ->label('Internal name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Diwali 2026 launch popup')
                        ->helperText('Only for this list — visitors never see it.')
                        ->columnSpanFull(),

                    TextInput::make('eyebrow')
                        ->label('Eyebrow')
                        ->maxLength(60)
                        ->placeholder('LIMITED TIME')
                        ->helperText('Small pill above the headline.'),

                    TextInput::make('headline')
                        ->label('Headline')
                        ->maxLength(120)
                        ->placeholder('Flat 20% off this Diwali')
                        ->helperText('Falls back to the internal name if left blank.'),

                    Textarea::make('body')
                        ->label('Body')
                        ->rows(3)
                        ->maxLength(400)
                        ->placeholder('On every monthly ration combo above ₹2,999. Valid at both Vinayak Stores stores.')
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->label('Image')
                        ->image()
                        ->disk('public')
                        ->directory('popups')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('Optional. Shown across the top of the popup — a wide 16:9 banner fits best.')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Button')
                ->description('One action. Leave the button text blank for a popup that only announces something.')
                ->icon('heroicon-o-cursor-arrow-rays')
                ->schema([
                    TextInput::make('cta_text')
                        ->label('Button text')
                        ->maxLength(40)
                        ->placeholder('See the offers')
                        ->live(onBlur: true),

                    Toggle::make('cta_opens_enquiry')
                        ->label('Button opens the enquiry form')
                        ->helperText('Instead of going to a link, it opens the same contact modal the rest of the site uses.')
                        ->live(),

                    TextInput::make('cta_url')
                        ->label('Button link')
                        ->maxLength(255)
                        ->placeholder('/promotions')
                        ->helperText('A path on this site like /promotions, or a full https:// address.')
                        ->visible(fn (Get $get) => ! $get('cta_opens_enquiry'))
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Where and how often')
                ->description('A popup interrupts, so the frequency here is what keeps it from wearing out its welcome.')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Radio::make('audience')
                        ->label('Show on')
                        ->options([
                            'all' => 'Every page of the site',
                            'selected' => 'Only the pages I pick',
                        ])
                        ->default('all')
                        ->live()
                        ->columnSpanFull(),

                    CheckboxList::make('pages')
                        ->label('Pages')
                        ->options(PromoPopup::PAGES)
                        ->columns(2)
                        ->required(fn (Get $get) => $get('audience') === 'selected')
                        ->visible(fn (Get $get) => $get('audience') === 'selected')
                        ->columnSpanFull(),

                    Select::make('frequency')
                        ->label('Show it again')
                        ->options(PromoPopup::FREQUENCIES)
                        ->default('once_per_session')
                        ->native(false)
                        ->required()
                        ->helperText('Tracked in the visitor\'s own browser, so clearing site data resets it.'),

                    TextInput::make('delay_seconds')
                        ->label('Open after')
                        ->numeric()
                        ->default(3)
                        ->minValue(0)
                        ->maxValue(120)
                        ->suffix('seconds')
                        ->helperText('A few seconds in reads as less aggressive than opening instantly.'),

                    TextInput::make('sort_order')
                        ->label('Priority')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Only one popup ever opens on a page. If two are live for the same page, the lower number wins.')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Schedule')
                ->description('Times are '.config('app.timezone').'. The popup stops opening at its expiry on its own — no need to come back and switch it off.')
                ->icon('heroicon-o-clock')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('The master switch. Off means it never opens, whatever the dates say.')
                        ->live()
                        ->columnSpanFull(),

                    DateTimePicker::make('publish_at')
                        ->label('Starts')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d M Y, H:i')
                        ->helperText('Blank = starts as soon as it is active.')
                        ->live(onBlur: true)
                        ->beforeOrEqual('expire_at')
                        ->validationMessages([
                            'before_or_equal' => 'The start time has to be before the expiry time.',
                        ]),

                    DateTimePicker::make('expire_at')
                        ->label('Ends')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d M Y, H:i')
                        ->helperText('Blank = runs until you switch it off.')
                        ->live(onBlur: true)
                        ->afterOrEqual('publish_at')
                        ->validationMessages([
                            'after_or_equal' => 'The expiry time has to be after the start time.',
                        ]),

                    Text::make(fn (Get $get) => self::statusLine($get))
                        ->badge()
                        ->color(fn (Get $get) => self::statusColour($get))
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    private static function statusLine(Get $get): string
    {
        if (! $get('is_active')) {
            return 'Hidden — "Active" is off, so this popup never opens.';
        }

        $publish = self::asDate($get('publish_at'));
        $expire = self::asDate($get('expire_at'));

        if ($expire && $expire->isPast()) {
            return 'Finished '.$expire->diffForHumans().' — no longer opening.';
        }

        if ($publish && $publish->isFuture()) {
            return 'Scheduled — starts '.$publish->diffForHumans().($expire ? ', ends '.$expire->diffForHumans() : '.');
        }

        return 'Opening now'.($expire ? ' — ends '.$expire->diffForHumans().'.' : ' — no end date set.');
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
