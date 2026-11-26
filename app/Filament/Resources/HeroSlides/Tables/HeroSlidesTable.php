<?php

namespace App\Filament\Resources\HeroSlides\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HeroSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->label('')
                    ->size(60),

                // title_html carries markup for the gradient words, so it is
                // stripped here rather than rendered inside a table cell.
                TextColumn::make('title_html')
                    ->label('Headline')
                    ->formatStateUsing(fn (?string $state) => trim(preg_replace('/\s+/', ' ', strip_tags((string) $state))))
                    ->wrap()
                    ->weight('semibold')
                    ->searchable(),

                TextColumn::make('btn_primary_text')
                    ->label('Buttons')
                    ->formatStateUsing(fn (?string $state) => strip_tags((string) $state))
                    ->description(fn ($record) => strip_tags((string) $record->btn_navy_text))
                    ->color('gray')
                    ->toggleable(),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All slides'),
            ])
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateHeading('No hero slides')
            ->emptyStateDescription('The homepage hero rotates through these in order.')
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
