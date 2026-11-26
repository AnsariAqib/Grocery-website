<?php

namespace App\Filament\Resources\PromoPopups;

use App\Filament\Resources\PromoPopups\Pages\CreatePromoPopup;
use App\Filament\Resources\PromoPopups\Pages\EditPromoPopup;
use App\Filament\Resources\PromoPopups\Pages\ListPromoPopups;
use App\Filament\Resources\PromoPopups\Schemas\PromoPopupForm;
use App\Filament\Resources\PromoPopups\Tables\PromoPopupsTable;
use App\Models\PromoPopup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PromoPopupResource extends Resource
{
    protected static ?string $model = PromoPopup::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-window';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Promo Popups';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Promo Popup';

    protected static ?string $pluralModelLabel = 'Promo Popups';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PromoPopupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromoPopupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromoPopups::route('/'),
            'create' => CreatePromoPopup::route('/create'),
            'edit' => EditPromoPopup::route('/{record}/edit'),
        ];
    }

    /** Only one popup shows at a time, so surface how many are live right now. */
    public static function getNavigationBadge(): ?string
    {
        $live = PromoPopup::active()->count();

        return $live > 0 ? (string) $live : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return PromoPopup::active()->count() > 1 ? 'warning' : 'success';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'headline'];
    }
}
