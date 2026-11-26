<?php

namespace App\Filament\Resources\Subcategories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubcategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->groups(['category.name'])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn () => Category::query()
                        ->orderBy('type')
                        ->orderBy('sort_order')
                        ->pluck('name', 'id')
                        ->all()),

                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All subcategories'),
            ])
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('No subcategories yet')
            ->emptyStateDescription('Subcategories are the varieties shown inside a category card, and every product belongs to one.')
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
