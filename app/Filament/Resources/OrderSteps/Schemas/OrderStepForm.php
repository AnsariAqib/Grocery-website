<?php

namespace App\Filament\Resources\OrderSteps\Schemas;

use App\Filament\Helpers\IconHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order process step')
                ->description('The steps that scroll across the B2B Order Process strip on the B2B Categories page.')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Share your requirement'),

                    Select::make('icon')
                        ->options(IconHelper::getFontAwesomeSolidIcons())
                        ->allowHtml()
                        ->searchable()
                        ->default('fa-list-check')
                        ->helperText('Pick any icon from the library.'),

                    Textarea::make('description')
                        ->rows(3)
                        ->maxLength(600)
                        ->columnSpanFull(),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Steps are numbered in this order.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
