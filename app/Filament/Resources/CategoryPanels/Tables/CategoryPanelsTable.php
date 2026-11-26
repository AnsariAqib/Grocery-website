<?php

namespace App\Filament\Resources\CategoryPanels\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoryPanelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('art_image')
                    ->disk('public')
                    ->label('')
                    ->size(52),

                TextColumn::make('heading')
                    ->weight('semibold')
                    ->description(fn ($record) => $record->subheading),

                TextColumn::make('badge_text')
                    ->label('Badge')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('type')
                    ->badge()
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('type')
            ->paginated(false)
            // Exactly two panels exist and the site reads them by type, so this
            // screen edits rather than creates or deletes.
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
