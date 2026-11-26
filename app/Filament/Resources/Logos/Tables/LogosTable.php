<?php

namespace App\Filament\Resources\Logos\Tables;

use App\Models\Logo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogosTable
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
                    ->formatStateUsing(fn (string $state) => $state === Logo::TYPE_CLIENT
                        ? 'Corporate client'
                        : 'Brand agency')
                    ->color(fn (string $state) => $state === Logo::TYPE_CLIENT ? 'primary' : 'gray')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->groups(['type'])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        Logo::TYPE_CLIENT => 'Trusted Corporate Clients',
                        Logo::TYPE_BRAND => 'Authorised Brand Agencies',
                    ]),
            ])
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->emptyStateHeading('No logos yet')
            ->emptyStateDescription('These scroll in the two marquees on the homepage.')
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
