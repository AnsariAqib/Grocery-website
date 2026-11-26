<?php

namespace App\Filament\Resources\Catalogues\Schemas;

use App\Models\Catalogue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CatalogueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Catalogue')
                ->description('Which download button on the website this file belongs to.')
                ->schema([
                    Select::make('type')
                        ->label('Catalogue Type')
                        ->options(Catalogue::TYPES)
                        ->default(Catalogue::TYPE_RETAIL)
                        ->required()
                        ->native(false)
                        ->helperText('Retail feeds the "Supermarket / Retail Master Catalog" buttons, B2B the "Corporate / B2B Master Catalog" ones.'),

                    TextInput::make('title')
                        ->label('Title')
                        ->placeholder('e.g. Retail Master Catalogue 2026')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Also used as the downloaded file name.'),

                    Textarea::make('description')
                        ->label('Short Description')
                        ->placeholder('e.g. Complete supermarket product range with pack sizes.')
                        ->rows(2)
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('PDF File')
                ->description('Upload the catalogue PDF. Until a file is uploaded and the row is active, the website shows a "coming soon" popup instead of downloading.')
                ->schema([
                    FileUpload::make('file')
                        ->label('Catalogue PDF')
                        ->disk('public')
                        ->directory('catalogues')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(51200)
                        ->openable()
                        ->downloadable()
                        ->helperText('PDF only, up to 50MB. Uploading a new file here replaces the one on the website.')
                        ->columnSpanFull(),

                    TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->helperText('If a type has more than one active file, the lowest number is the one the website serves.'),

                    Toggle::make('is_active')
                        ->label('Live on Website')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
