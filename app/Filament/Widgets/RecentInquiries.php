<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Models\Inquiry;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInquiries extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Customer Inquiries';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Inquiry::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('inquiry_type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'retail' => 'info',
                        'b2b' => 'success',
                        'dealership' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('product_name')
                    ->label('Product')
                    ->placeholder('-')
                    ->limit(25),
                TextColumn::make('category')
                    ->formatStateUsing(fn ($state) => $state ? ucfirst(str_replace('-', ' ', $state)) : null)
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since(),
            ])
            ->paginated(false)
            ->actions([
                // Resolved from the resource, not hardcoded: the literal
                // '/admin/user-inquiries' this used to point at is a 404.
                Action::make('view')
                    ->url(fn ($record) => InquiryResource::getUrl('index', ['tableSearch' => $record->name]))
                    ->icon('heroicon-m-eye')
                    ->color('primary'),
            ]);
    }
}
