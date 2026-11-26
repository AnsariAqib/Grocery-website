<?php

namespace App\Filament\Resources\Leaders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LeadersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->disk('public')
                    ->label('')
                    ->circular()
                    ->size(44)
                    ->defaultImageUrl(null),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('company')
                    ->label('Company / division')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Bio')
                    ->wrap()
                    ->color('gray')
                    ->limit(90),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All members'),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No team members yet')
            ->emptyStateDescription('These are the cards in the Our Leadership Team section on the About Us page.')
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
