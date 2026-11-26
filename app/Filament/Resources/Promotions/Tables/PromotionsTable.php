<?php

namespace App\Filament\Resources\Promotions\Tables;

use App\Models\Promotion;
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
use Illuminate\Database\Eloquent\Builder;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->label('')
                    ->size(44),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->subtitle ?: $record->description)
                    ->wrap(),

                TextColumn::make('badge')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('discount')
                    ->label('Offer')
                    ->weight('semibold'),

                TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Promotion::GROUPS[$state] ?? ucfirst((string) $state))
                    ->color('gray')
                    ->sortable(),

                // What the storefront is actually doing with this row right
                // now — is_active alone never told you whether the schedule
                // had already retired it.
                TextColumn::make('schedule_status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Promotion $record) => $record->schedule_status)
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Promotion::STATUS_LIVE => 'Live',
                        Promotion::STATUS_SCHEDULED => 'Scheduled',
                        Promotion::STATUS_EXPIRED => 'Expired',
                        default => 'Hidden',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        Promotion::STATUS_LIVE => 'heroicon-m-check-circle',
                        Promotion::STATUS_SCHEDULED => 'heroicon-m-clock',
                        Promotion::STATUS_EXPIRED => 'heroicon-m-x-circle',
                        default => 'heroicon-m-eye-slash',
                    })
                    ->color(fn (string $state) => match ($state) {
                        Promotion::STATUS_LIVE => 'success',
                        Promotion::STATUS_SCHEDULED => 'warning',
                        Promotion::STATUS_EXPIRED => 'danger',
                        default => 'gray',
                    })
                    ->description(fn (Promotion $record) => match ($record->schedule_status) {
                        Promotion::STATUS_SCHEDULED => 'starts '.$record->publish_at->diffForHumans(),
                        Promotion::STATUS_EXPIRED => 'ended '.$record->expire_at->diffForHumans(),
                        Promotion::STATUS_LIVE => $record->expire_at
                            ? 'ends '.$record->expire_at->diffForHumans()
                            : 'no expiry',
                        default => null,
                    }),

                ToggleColumn::make('show_on_home')
                    ->label('Home'),

                ToggleColumn::make('show_on_page')
                    ->label('Offers page'),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('publish_at')
                    ->label('Goes live')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Immediately')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expire_at')
                    ->label('Expires')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('validity')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('group')
                    ->options(Promotion::GROUPS),

                // The one the admin reaches for after a campaign: "show me
                // everything that has already run out".
                SelectFilter::make('schedule_status')
                    ->label('Status')
                    ->options([
                        Promotion::STATUS_LIVE => 'Live now',
                        Promotion::STATUS_SCHEDULED => 'Scheduled',
                        Promotion::STATUS_EXPIRED => 'Expired',
                        Promotion::STATUS_HIDDEN => 'Hidden (not active)',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        Promotion::STATUS_LIVE => $query->active(),
                        Promotion::STATUS_SCHEDULED => $query->where('is_active', true)->scheduled(),
                        Promotion::STATUS_EXPIRED => $query->where('is_active', true)->expired(),
                        Promotion::STATUS_HIDDEN => $query->where('is_active', false),
                        default => $query,
                    }),

                TernaryFilter::make('show_on_home')
                    ->label('Home page')
                    ->placeholder('Anywhere'),
            ])
            ->emptyStateIcon('heroicon-o-megaphone')
            ->emptyStateHeading('No promotions yet')
            ->emptyStateDescription('Home shows a four-card row; the offers page groups them by category.')
            ->paginated([25, 50, 100])
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
