<?php

namespace App\Filament\Resources\Subcategories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SubcategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Subcategory')
                ->schema([
                    Select::make('category_type')
                        ->label('Which type of category?')
                        ->options([
                            Category::TYPE_STORE => 'Store Categories (For Homes)',
                            Category::TYPE_B2B => 'B2B Categories (For Businesses)',
                        ])
                        ->live()
                        ->dehydrated(false)
                        ->afterStateHydrated(function (Select $component, ?object $record) {
                            if ($record && $record->category) {
                                $component->state($record->category->type);
                            }
                        })
                        ->afterStateUpdated(fn (Set $set) => $set('category_id', null))
                        ->required(),

                    Select::make('category_id')
                        ->label('Category')
                        ->relationship(
                            name: 'category',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query, Get $get) => $query->when($get('category_type'), fn ($q, $type) => $q->where('type', $type))
                                ->orderBy('sort_order')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state, ?object $record) {
                            if ($record === null) {
                                $set('slug', Str::slug((string) $state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Unique within its category.'),

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
