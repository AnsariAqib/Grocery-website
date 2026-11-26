<?php

namespace App\Filament\Resources\GalleryImages\Tables;

use App\Models\GalleryImage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GalleryImagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // A video row has no uploaded file to show, so the still comes
                // from the model rather than straight off the disk.
                ImageColumn::make('poster_url')
                    ->label('')
                    ->size(56),

                TextColumn::make('media_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state === GalleryImage::TYPE_VIDEO ? 'Video' : 'Photo')
                    ->color(fn (?string $state) => $state === GalleryImage::TYPE_VIDEO ? 'warning' : 'gray'),

                TextColumn::make('title')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->caption)
                    ->placeholder('Untitled'),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('media_type')
                    ->label('Kind')
                    ->options([
                        GalleryImage::TYPE_PHOTO => 'Photos',
                        GalleryImage::TYPE_VIDEO => 'Videos',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All items'),
            ])
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateHeading('Nothing in the gallery yet')
            ->emptyStateDescription('Photos rotate beside the Featured sections on the home page; videos play from YouTube under their own heading.')
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
