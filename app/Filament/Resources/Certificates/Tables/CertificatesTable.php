<?php

namespace App\Filament\Resources\Certificates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_image')
                    ->disk('public')
                    ->label('')
                    ->size(44),

                TextColumn::make('brand_name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->description)
                    ->wrap(),

                TextColumn::make('title')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('signatory')
                    ->color('gray')
                    ->toggleable(),

                IconColumn::make('pdf_file')
                    ->label('PDF')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-minus-small')
                    ->getStateUsing(fn ($record) => filled($record->pdf_file)),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->emptyStateHeading('No certificates yet')
            ->emptyStateDescription('Each one becomes a card in the Authorised Distributorship marquee.')
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
