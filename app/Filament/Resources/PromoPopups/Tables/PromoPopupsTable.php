<?php

namespace App\Filament\Resources\PromoPopups\Tables;

use App\Models\PromoPopup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromoPopupsTable
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
                    ->label('Popup')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (PromoPopup $record) => $record->headline)
                    ->wrap(),

                TextColumn::make('schedule_status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (PromoPopup $record) => $record->schedule_status)
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        PromoPopup::STATUS_LIVE => 'Opening',
                        PromoPopup::STATUS_SCHEDULED => 'Scheduled',
                        PromoPopup::STATUS_EXPIRED => 'Finished',
                        default => 'Hidden',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        PromoPopup::STATUS_LIVE => 'heroicon-m-check-circle',
                        PromoPopup::STATUS_SCHEDULED => 'heroicon-m-clock',
                        PromoPopup::STATUS_EXPIRED => 'heroicon-m-x-circle',
                        default => 'heroicon-m-eye-slash',
                    })
                    ->color(fn (string $state) => match ($state) {
                        PromoPopup::STATUS_LIVE => 'success',
                        PromoPopup::STATUS_SCHEDULED => 'warning',
                        PromoPopup::STATUS_EXPIRED => 'danger',
                        default => 'gray',
                    })
                    ->description(fn (PromoPopup $record) => match ($record->schedule_status) {
                        PromoPopup::STATUS_SCHEDULED => 'starts '.$record->publish_at->diffForHumans(),
                        PromoPopup::STATUS_EXPIRED => 'ended '.$record->expire_at->diffForHumans(),
                        PromoPopup::STATUS_LIVE => $record->expire_at
                            ? 'ends '.$record->expire_at->diffForHumans()
                            : 'no end date',
                        default => null,
                    }),

                TextColumn::make('audience')
                    ->label('Pages')
                    ->badge()
                    ->color('gray')
                    ->state(function (PromoPopup $record): string {
                        if ($record->audience !== 'selected') {
                            return 'Every page';
                        }

                        return collect($record->pages ?? [])
                            ->map(fn (string $page) => PromoPopup::PAGES[$page] ?? $page)
                            ->join(', ') ?: 'No pages picked';
                    })
                    ->wrap(),

                TextColumn::make('frequency')
                    ->label('Repeats')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'always' => 'Every view',
                        'once_per_visit' => 'Once per visit',
                        'once_per_session' => 'Once per session',
                        'once_per_day' => 'Once a day',
                        'once_ever' => 'Once ever',
                        default => $state,
                    })
                    ->color('gray')
                    ->description(fn (PromoPopup $record) => $record->delay_seconds > 0
                        ? 'opens after '.$record->delay_seconds.'s'
                        : 'opens immediately'),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('publish_at')
                    ->label('Starts')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Immediately')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expire_at')
                    ->label('Ends')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('schedule_status')
                    ->label('Status')
                    ->options([
                        PromoPopup::STATUS_LIVE => 'Opening now',
                        PromoPopup::STATUS_SCHEDULED => 'Scheduled',
                        PromoPopup::STATUS_EXPIRED => 'Finished',
                        PromoPopup::STATUS_HIDDEN => 'Hidden (not active)',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        PromoPopup::STATUS_LIVE => $query->active(),
                        PromoPopup::STATUS_SCHEDULED => $query->where('is_active', true)->scheduled(),
                        PromoPopup::STATUS_EXPIRED => $query->where('is_active', true)->expired(),
                        PromoPopup::STATUS_HIDDEN => $query->where('is_active', false),
                        default => $query,
                    }),

                SelectFilter::make('frequency')
                    ->options(PromoPopup::FREQUENCIES),
            ])
            ->emptyStateIcon('heroicon-o-window')
            ->emptyStateHeading('No popups yet')
            ->emptyStateDescription('A popup opens over the storefront on the pages you pick, between the dates you set. Only one shows at a time.')
            ->paginated([25, 50, 100])
            ->recordActions([
                EditAction::make(),
                // Campaigns tend to be re-run with new dates rather than
                // written from scratch.
                ReplicateAction::make()
                    ->label('Duplicate')
                    ->excludeAttributes(['publish_at', 'expire_at'])
                    ->beforeReplicaSaved(function (PromoPopup $replica): void {
                        $replica->title = $replica->title.' (copy)';
                        $replica->is_active = false;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
