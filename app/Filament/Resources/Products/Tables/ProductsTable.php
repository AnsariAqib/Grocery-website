<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Category;
use App\Services\ProductCsvExporter;
use Filament\Actions\BulkAction;
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
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductsTable
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
                    // A bulk-imported catalogue is looked up by the supplier's
                    // code as often as by name, so one search box covers both.
                    ->searchable(['name', 'sku'])
                    ->sortable()
                    ->weight('semibold')
                    // Brand and variety on the second line rather than in two
                    // more columns — the table has to fit a laptop without
                    // scrolling sideways to reach Edit.
                    ->description(fn ($record) => collect([$record->brand, $record->subcategory?->name])
                        ->filter()
                        ->implode('  ·  '))
                    ->wrap(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->placeholder('—')
                    ->color('gray')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subcategory.category.name')
                    // Shrink to the content: the slack goes to the product name,
                    // which is the column anyone actually reads.
                    ->width('1%')
                    ->label('Category')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('price')
                    ->width('1%')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(fn ($state, $record) => $record->price_on_request
                        ? 'On request'
                        : $state)
                    ->color(fn ($record) => $record->price_on_request ? 'gray' : null),

                TextColumn::make('pack_size')
                    ->width('1%')
                    ->label('Pack')
                    ->placeholder('—')
                    // A product with pack sizes leaves this column empty, which
                    // read as "no size" rather than "several". An import makes
                    // plenty of those, so the count stands in.
                    ->formatStateUsing(function ($state, $record) {
                        $sizes = count((array) $record->variants);

                        return $sizes > 0 ? $sizes.' sizes' : $state;
                    })
                    ->color(fn ($record) => count((array) $record->variants) > 0 ? 'gray' : null)
                    // Off by default, on from the columns menu. Something had to
                    // give for the table to fit the card without scrolling, and
                    // the pack is the one thing here that is also on every row's
                    // second line once a product has sizes.
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('badge')
                    ->badge()
                    ->color(fn ($record) => null)
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_featured')
                    ->width('1%')
                    ->label('Featured'),

                // Flipped straight from the list — the common edit by far.
                ToggleColumn::make('is_active')
                    ->width('1%')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(fn () => Category::query()
                        ->orderBy('type')
                        ->orderBy('sort_order')
                        ->pluck('name', 'id')
                        ->all())
                    ->query(fn ($query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereRelation('subcategory', 'category_id', $data['value'])
                        : $query),

                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All products')
                    ->trueLabel('Live only')
                    ->falseLabel('Hidden only'),
            ])
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Add your first product and it appears in the catalogue straight away.')
            ->paginated([25, 50, 100])
            // Icon buttons, not labelled ones: the labels pushed the actions
            // column past the edge of the screen, so Edit could only be reached
            // by scrolling the table sideways.
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // A price list for one supplier, or one category's sheet to
                    // hand back after editing — the same format the full export
                    // and the bulk import use.
                    BulkAction::make('exportSelected')
                        ->label('Export selected to CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(fn (Collection $records): StreamedResponse => app(ProductCsvExporter::class)->download($records)),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
