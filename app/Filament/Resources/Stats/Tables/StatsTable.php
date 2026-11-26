<?php

namespace App\Filament\Resources\Stats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class StatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('target')
                    ->label('Counts up to')
                    ->numeric()
                    ->formatStateUsing(fn ($state, $record) => number_format((int) $state).$record->suffix)
                    ->alignEnd(),

                TextColumn::make('icon')
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->emptyStateHeading('No stats')
            ->emptyStateDescription('These are the counters in the band under the homepage hero.')
            ->paginated(false)
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
