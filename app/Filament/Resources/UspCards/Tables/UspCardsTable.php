<?php

namespace App\Filament\Resources\UspCards\Tables;

use App\Models\UspCard;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UspCardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->description),

                TextColumn::make('scope')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        UspCard::SCOPE_STORE => 'Store Categories',
                        UspCard::SCOPE_B2B => 'B2B Categories',
                        default => 'Both pages',
                    })
                    ->color(fn (string $state) => $state === UspCard::SCOPE_BOTH ? 'gray' : 'primary'),

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
                SelectFilter::make('scope')
                    ->options([
                        UspCard::SCOPE_BOTH => 'Both pages',
                        UspCard::SCOPE_STORE => 'Store Categories',
                        UspCard::SCOPE_B2B => 'B2B Categories',
                    ]),
            ])
            ->emptyStateIcon('heroicon-o-sparkles')
            ->emptyStateHeading('No USP cards yet')
            ->emptyStateDescription('These sit under the hero on the two category pages.')
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
