<?php

namespace App\Filament\Resources\CategoryBanners\Tables;

use App\Models\CategoryBanner;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoryBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('background_image')
                    ->disk('public')
                    ->label('Image')
                    ->size(70)
                    ->extraImgAttributes(['class' => 'rounded-md object-cover'])
                    ->placeholder('Gradient default'),

                TextColumn::make('type')
                    ->label('Banner Page')
                    ->formatStateUsing(fn ($state, CategoryBanner $record) => $record->type_label)
                    ->weight('bold')
                    ->description(fn (CategoryBanner $record) => $record->title.' '.$record->highlight),

                TextColumn::make('badge_text')
                    ->label('Badge')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('cta_text')
                    ->label('CTA Button')
                    ->badge()
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Live'),
            ])
            ->defaultSort('type')
            ->paginated(false)
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
