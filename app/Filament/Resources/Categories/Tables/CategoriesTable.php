<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->label('')
                    ->size(44),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === Category::TYPE_STORE ? 'Store' : 'B2B')
                    ->color(fn (string $state) => $state === Category::TYPE_STORE ? 'primary' : 'gray')
                    ->sortable(),

                // Doubles as the accessible table view for the dashboard chart.
                TextColumn::make('subcategories_count')
                    ->label('Subcategories')
                    ->counts('subcategories')
                    ->alignCenter(),

                TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->alignCenter(),

                TextColumn::make('icon')
                    ->label('Icon')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_featured')
                    ->label('Featured'),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        Category::TYPE_STORE => 'Store (For Homes)',
                        Category::TYPE_B2B => 'B2B (For Businesses)',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All categories')
                    ->trueLabel('Live only')
                    ->falseLabel('Hidden only'),
            ])
            ->emptyStateIcon('heroicon-o-squares-2x2')
            ->emptyStateHeading('No categories yet')
            ->emptyStateDescription('The home page shows the first 10 of each type, in this order.')
            ->paginated([25, 50, 100])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
