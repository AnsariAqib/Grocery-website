<?php

namespace App\Filament\Resources\UspCards\Schemas;

use App\Filament\Helpers\IconHelper;
use App\Models\UspCard;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UspCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('USP card')
                ->description('The icon cards under the hero on the Store Categories and B2B Categories pages.')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('100% Genuine Products'),

                    Select::make('scope')
                        ->label('Show on')
                        ->options([
                            UspCard::SCOPE_BOTH => 'Both category pages',
                            UspCard::SCOPE_STORE => 'Store Categories only',
                            UspCard::SCOPE_B2B => 'B2B Categories only',
                        ])
                        ->default(UspCard::SCOPE_BOTH)
                        ->required(),

                    Select::make('icon')
                        ->options(IconHelper::getFontAwesomeSolidIcons())
                        ->allowHtml()
                        ->searchable()
                        ->default('fa-shield-halved')
                        ->helperText('Pick any icon from the library.'),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers come first.'),

                    Textarea::make('description')
                        ->rows(2)
                        ->maxLength(400)
                        ->placeholder('Sourced directly from manufacturers and authorised distributors.')
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
