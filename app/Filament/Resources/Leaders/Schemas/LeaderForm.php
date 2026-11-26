<?php

namespace App\Filament\Resources\Leaders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Team member')
                ->description('These are the cards in the Our Leadership Team section on the About Us page.')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Mahavir Jain'),

                    TextInput::make('company')
                        ->label('Company / division')
                        ->maxLength(255)
                        ->placeholder('Vinayak Stores')
                        ->helperText('Shown in orange under the name.'),

                    Textarea::make('description')
                        ->label('Short bio')
                        ->rows(3)
                        ->maxLength(600)
                        ->columnSpanFull(),

                    FileUpload::make('photo')
                        ->label('Photo')
                        ->image()
                        ->disk('public')
                        ->directory('leaders')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->imageCropAspectRatio('1:1')
                        ->helperText('Optional. A square portrait works best — without one the card shows the initials.')
                        ->columnSpanFull(),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers appear first.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
