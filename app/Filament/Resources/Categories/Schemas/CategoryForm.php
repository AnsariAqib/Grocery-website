<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Helpers\IconHelper;
use App\Models\Category;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state, ?object $record) {
                            if ($record === null) {
                                $set('slug', Str::slug((string) $state));
                                $set('home_label', (string) $state);
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Select::make('type')
                        ->options([
                            Category::TYPE_STORE => 'Store (For Homes)',
                            Category::TYPE_B2B => 'B2B (For Businesses)',
                        ])
                        ->default(Category::TYPE_STORE)
                        ->required()
                        ->helperText('Decides which home page panel the tile appears in. Only Store categories show on the Store Categories page.'),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers come first. The home page shows the first 10 of each type.'),
                ])
                ->columns(2),

            Section::make('Home page tile')
                ->schema([
                    Select::make('icon')
                        ->options(IconHelper::getFontAwesomeSolidIcons())
                        ->allowHtml()
                        ->searchable()
                        ->helperText('Select a visual icon for the category.'),

                    ColorPicker::make('icon_color')
                        ->label('Icon colour'),

                    TextInput::make('home_label')
                        ->label('Tile label')
                        ->maxLength(255)
                        ->helperText('Shown on the home page tile. A <br> forces a line break so tiles keep an even height.')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Category card')
                ->schema([
                    FileUpload::make('image')
                        ->image()
                        ->disk('public')
                        ->directory('categories')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('Cut-out artwork for the card on the Store Categories page.'),

                    ColorPicker::make('badge_bg')
                        ->label('Accent colour'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Toggle::make('is_featured')
                        ->label('Featured on the home page')
                        ->helperText('Shows in the Featured Categories rail.')
                        ->default(false),
                ])
                ->columns(2),
        ]);
    }
}
