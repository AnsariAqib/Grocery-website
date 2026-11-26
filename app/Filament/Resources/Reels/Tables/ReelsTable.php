<?php

namespace App\Filament\Resources\Reels\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // The raw disk column, not the cover_url accessor: that accessor
                // may hand back a YouTube CDN address, which this column would
                // try to resolve against the public disk.
                ImageColumn::make('cover_image')
                    ->disk('public')
                    ->label('')
                    ->placeholder('—')
                    ->size(60),

                TextColumn::make('title')
                    ->label('Heading')
                    ->placeholder('—')
                    ->description(fn ($record) => $record->caption)
                    ->wrap()
                    ->weight('semibold')
                    ->searchable(),

                // The accessor, not the stored link: a mistyped URL shows the
                // red "Invalid link" instead of a video id.
                TextColumn::make('youtube_id')
                    ->label('YouTube')
                    ->placeholder('Invalid link')
                    ->badge()
                    ->color(fn ($state) => filled($state) ? 'success' : 'danger'),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Visibility')
                    ->placeholder('All shorts'),
            ])
            ->emptyStateIcon('heroicon-o-play-circle')
            ->emptyStateHeading('No shorts yet')
            ->emptyStateDescription('Paste a YouTube Shorts link and it appears on the promotions page, below the offers.')
            ->paginated(false)
            ->recordActions([
                Action::make('open')
                    ->label('View')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => $record->permalink, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => filled($record->youtube_id)),
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
