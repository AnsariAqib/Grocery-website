<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->role),

                TextColumn::make('quote')
                    ->limit(70)
                    ->color('gray')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Stars')
                    ->formatStateUsing(fn (?int $state) => str_repeat('★', max(1, min(5, (int) $state))))
                    ->color('warning'),

                TextColumn::make('tag')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All reviews'),
            ])
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->emptyStateHeading('No reviews yet')
            ->emptyStateDescription('Add one and the reviews section appears on the home page.')
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
