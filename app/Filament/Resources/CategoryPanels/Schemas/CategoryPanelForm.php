<?php

namespace App\Filament\Resources\CategoryPanels\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryPanelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Panel')
                ->description('The two big cards in the home page categories section. The tiles inside them come from the Categories screen — the first 10 of each type.')
                ->schema([
                    TextInput::make('type')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('store = For Homes, b2b = For Businesses.'),

                    TextInput::make('badge_text')
                        ->label('Badge')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('FOR HOMES'),

                    TextInput::make('heading')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('subheading')->maxLength(255),

                    TextInput::make('cta_text')
                        ->label('Button text')
                        ->maxLength(255),

                    TextInput::make('cta_url')
                        ->label('Button link')
                        ->maxLength(255)
                        ->placeholder('/products?segment=retail')
                        ->helperText('Where the button goes. A path on this site (/products, /contact) or a full https:// address. Leave blank and the button opens the enquiry form, as it does today.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    FileUpload::make('art_image')
                        ->label('Cut-out artwork')
                        ->image()
                        ->disk('public')
                        ->directory('panels')
                        ->acceptedFileTypes(['image/png'])
                        ->maxSize(25600)
                        ->helperText('Transparent cut-out PNG only — an image with a background will show as a visible box on the card.')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
