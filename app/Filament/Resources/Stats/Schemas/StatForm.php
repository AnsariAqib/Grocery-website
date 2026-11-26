<?php

namespace App\Filament\Resources\Stats\Schemas;

use App\Filament\Helpers\IconHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Counter')
                ->description('The animated counters in the stats ribbon on the home page.')
                ->schema([
                    TextInput::make('label')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Happy Customers')
                        ->helperText('Displayed below the number.'),

                    TextInput::make('target')
                        ->required()
                        ->numeric()
                        ->placeholder('50000')
                        ->helperText('The number the counter animates up to.'),

                    TextInput::make('suffix')
                        ->maxLength(20)
                        ->placeholder('+')
                        ->helperText('Appended after the number, e.g. "+" or "K".'),

                    Select::make('icon')
                        ->options(IconHelper::getFontAwesomeSolidIcons())
                        ->allowHtml()
                        ->searchable()
                        ->helperText('Select a visual icon.'),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Left-to-right order in the ribbon.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
