<?php

namespace App\Filament\Resources\Logos\Schemas;

use App\Models\Logo;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LogoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Logo')
                ->schema([
                    Select::make('type')
                        ->options([
                            Logo::TYPE_CLIENT => 'Trusted Corporate Client',
                            Logo::TYPE_BRAND => 'Authorised Brand Agency',
                        ])
                        ->default(Logo::TYPE_CLIENT)
                        ->required()
                        ->helperText('Chooses which of the two home page marquees it scrolls in.'),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Used as the image alt text.'),

                    FileUpload::make('image')
                        ->image()
                        ->disk('public')
                        ->directory('logos')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('Logo on a transparent or white background.')
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
