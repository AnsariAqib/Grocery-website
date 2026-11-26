<?php

namespace App\Filament\Resources\Inquiries;

use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Models\Inquiry;
use App\Services\InquiryCsv;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|\UnitEnum|null $navigationGroup = 'Business';

    protected static ?string $navigationLabel = 'User Inquiries';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Inquiry';

    protected static ?string $pluralModelLabel = 'User Inquiries';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Count of inquiries from the last 7 days, so a fresh one is visible from
     * anywhere in the panel without opening the screen.
     */
    public static function getNavigationBadge(): ?string
    {
        $recent = static::getModel()::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return $recent > 0 ? (string) $recent : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Customer & Contact Details')
                    ->icon('heroicon-o-user-circle')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->weight(FontWeight::Bold)
                            ->icon('heroicon-m-user'),
                        TextEntry::make('phone')
                            ->label('Mobile Number')
                            ->icon('heroicon-m-phone')
                            ->copyable()
                            ->url(fn ($state) => $state ? "tel:{$state}" : null),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope')
                            ->placeholder('Not provided')
                            ->copyable()
                            ->url(fn ($state) => $state ? "mailto:{$state}" : null),
                        TextEntry::make('company')
                            ->label('Company / Business')
                            ->icon('heroicon-m-building-office-2')
                            ->placeholder('Not provided'),
                        TextEntry::make('industry')
                            ->label('Industry')
                            ->icon('heroicon-m-briefcase')
                            ->placeholder('Not provided'),
                        TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->icon('heroicon-m-calendar-days')
                            ->dateTime('d M Y, h:i A'),
                    ])
                    ->columns(3),

                Section::make('Inquiry & Requirement Details')
                    ->icon('heroicon-o-cube')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('inquiry_type')
                            ->label('Inquiry Type')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'retail' => 'info',
                                'b2b' => 'success',
                                'dealership' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => $state ? ucfirst($state).' Inquiry' : '-'),
                        TextEntry::make('product_name')
                            ->label('Product Inquired')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-m-cube')
                            ->placeholder('General / Not Specific'),
                        TextEntry::make('category')
                            ->label('Category Requirement')
                            ->formatStateUsing(fn ($state) => $state ? ucfirst(str_replace('-', ' ', $state)) : null)
                            ->placeholder('Not specified'),
                        TextEntry::make('message')
                            ->label('Message / Requirement Notes')
                            ->placeholder('No additional message provided.')
                            ->columnSpanFull()
                            ->prose(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inquiry Details')
                ->schema([
                    TextInput::make('name')->disabled(),
                    TextInput::make('phone')->tel()->disabled(),
                    TextInput::make('email')->email()->placeholder('-')->disabled(),
                    TextInput::make('company')->placeholder('-')->disabled(),
                    TextInput::make('industry')->placeholder('-')->disabled(),
                    TextInput::make('inquiry_type')
                        ->label('Inquiry Type')
                        ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : '-')
                        ->disabled(),
                    TextInput::make('product_name')
                        ->label('Product Inquired')
                        ->placeholder('N/A (General Inquiry)')
                        ->disabled(),
                    TextInput::make('category')
                        ->label('Category')
                        ->formatStateUsing(fn ($state) => $state ? ucfirst(str_replace('-', ' ', $state)) : null)
                        ->placeholder('N/A')
                        ->disabled(),
                    Textarea::make('message')->rows(4)->columnSpanFull()->disabled(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('inquiry_type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'retail' => 'info',
                        'b2b' => 'success',
                        'dealership' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->limit(35),
                TextColumn::make('company')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category')
                    ->formatStateUsing(fn ($state) => $state ? ucfirst(str_replace('-', ' ', $state)) : null)
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->modalWidth('4xl')
                    ->modalHeading(fn ($record) => "Inquiry Details - {$record->name}"),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Work a filtered slice in Excel — tick the rows, get a sheet.
                    BulkAction::make('exportSelected')
                        ->label('Export selected to CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => app(InquiryCsv::class)->download($records)),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
        ];
    }
}
