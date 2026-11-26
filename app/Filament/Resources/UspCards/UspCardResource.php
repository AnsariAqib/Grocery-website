<?php

namespace App\Filament\Resources\UspCards;

use App\Filament\Resources\UspCards\Pages\CreateUspCard;
use App\Filament\Resources\UspCards\Pages\EditUspCard;
use App\Filament\Resources\UspCards\Pages\ListUspCards;
use App\Filament\Resources\UspCards\Schemas\UspCardForm;
use App\Filament\Resources\UspCards\Tables\UspCardsTable;
use App\Models\UspCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UspCardResource extends Resource
{
    protected static ?string $model = UspCard::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'USP Cards';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'USP card';

    protected static ?string $pluralModelLabel = 'USP Cards';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return UspCardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UspCardsTable::configure($table);
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
            'index' => ListUspCards::route('/'),
            'create' => CreateUspCard::route('/create'),
            'edit' => EditUspCard::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description'];
    }
}
