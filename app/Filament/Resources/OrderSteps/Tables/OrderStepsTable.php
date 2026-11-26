<?php

namespace App\Filament\Resources\OrderSteps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrderStepsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('title')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->description),

                TextColumn::make('icon')
                    ->label('Icon')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All steps'),
            ])
            ->emptyStateIcon('heroicon-o-arrow-path-rounded-square')
            ->emptyStateHeading('No steps yet')
            ->emptyStateDescription('These make up the B2B Order Process strip.')
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
