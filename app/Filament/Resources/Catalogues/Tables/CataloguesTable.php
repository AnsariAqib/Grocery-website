<?php

namespace App\Filament\Resources\Catalogues\Tables;

use App\Models\Catalogue;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CataloguesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Catalogue')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->description)
                    ->wrap(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Catalogue::TYPES[$state] ?? $state)
                    ->color(fn ($state) => $state === Catalogue::TYPE_B2B ? 'info' : 'primary'),

                IconColumn::make('file')
                    ->label('PDF')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->falseColor('warning')
                    ->getStateUsing(fn ($record) => filled($record->file)),

                TextColumn::make('file_size_label')
                    ->label('Size')
                    ->placeholder('—')
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(Catalogue::TYPES),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateIcon('heroicon-o-document-arrow-down')
            ->emptyStateHeading('No catalogues uploaded yet')
            ->emptyStateDescription('Until a PDF is uploaded here, the website\'s catalogue download buttons show a "coming soon" popup.')
            ->paginated([25, 50])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn ($record) => $record->file_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => filled($record->file)),
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
