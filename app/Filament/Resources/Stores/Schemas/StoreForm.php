<?php

namespace App\Filament\Resources\Stores\Schemas;

use App\Models\Store;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Outlet')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('type')
                        ->label('Outlet type')
                        ->maxLength(255)
                        ->placeholder('Retail Supermarket Chain')
                        ->helperText('Shown as the badge over the photo.'),

                    Textarea::make('address')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->label('Cover photo')
                        ->image()
                        ->disk('public')
                        ->directory('stores')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('Shown first, and used everywhere a single photo is needed.')
                        ->columnSpanFull(),

                    FileUpload::make('gallery')
                        ->label('More photos')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->appendFiles()
                        ->disk('public')
                        ->directory('stores')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('The store card rotates through the cover photo and these, one every 10 seconds.')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Location on the map')
                ->description('Open the outlet in Google Maps, tap Share → Copy link, and paste it here. The exact pin is then used for "View on Map" and "Get Directions" on the website.')
                ->schema([
                    TextInput::make('map_url')
                        ->label('Google Maps link')
                        ->url()
                        ->maxLength(2048)
                        ->placeholder('https://maps.app.goo.gl/…')
                        ->helperText('Coordinates are read out of the link automatically when it contains them.')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                            if (blank($state) || (filled($get('latitude')) && filled($get('longitude')))) {
                                return;
                            }

                            if ($coords = Store::coordinatesFromUrl($state)) {
                                $set('latitude', $coords[0]);
                                $set('longitude', $coords[1]);
                            }
                        })
                        ->columnSpanFull(),

                    TextInput::make('latitude')
                        ->numeric()
                        ->minValue(-90)
                        ->maxValue(90)
                        ->step('any')
                        ->placeholder('21.1234567')
                        ->helperText('Optional — fill in only if you have the exact pin.'),

                    TextInput::make('longitude')
                        ->numeric()
                        ->minValue(-180)
                        ->maxValue(180)
                        ->step('any')
                        ->placeholder('72.1234567'),

                    TextInput::make('map_query')
                        ->label('Fallback map search')
                        ->maxLength(255)
                        ->helperText('Used only when there is no link and no coordinates. Falls back to the outlet name plus its address.')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Contact & listing')
                ->schema([
                    TextInput::make('phone')
                        ->label('Phone (as displayed)')
                        ->maxLength(255)
                        ->placeholder('9099081400 / 9099081409')
                        ->columnSpanFull(),

                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->maxLength(255)
                        ->placeholder('store@jaytulsi.com')
                        ->helperText('Shown on the store card and the Contact page.'),

                    TextInput::make('gst_number')
                        ->label('GST number')
                        ->maxLength(20)
                        ->placeholder('24ABCDE1234F1Z5')
                        ->helperText('Displayed on the outlet card for business customers.'),

                    // Stored as JSON so each number can be its own tel: link.
                    Repeater::make('phone_links')
                        ->label('Tap-to-call numbers')
                        ->simple(
                            TextInput::make('number')
                                ->tel()
                                ->required()
                                ->maxLength(15)
                        )
                        ->addActionLabel('Add a number')
                        ->helperText('One entry per number, without the +91.')
                        ->columnSpanFull(),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
