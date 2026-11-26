<?php

namespace App\Filament\Resources\Stores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->label('')
                    ->size(48),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->type),

                TextColumn::make('address')
                    ->wrap()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->icon('heroicon-m-phone'),

                TextColumn::make('latitude')
                    ->label('Map pin')
                    ->badge()
                    ->state(fn ($record) => $record->hasCoordinates()
                        ? 'Exact pin'
                        : (filled($record->map_url) ? 'Map link' : 'Address search'))
                    ->color(fn ($record) => $record->hasCoordinates() || filled($record->map_url) ? 'success' : 'warning')
                    ->url(fn ($record) => $record->map_link)
                    ->openUrlInNewTab(),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All stores'),
            ])
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateHeading('No stores yet')
            ->emptyStateDescription('These are the outlet cards on the Our Stores page.')
            ->paginated([25, 50])
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
