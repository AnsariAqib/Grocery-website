<?php

namespace App\Filament\Resources\Catalogues;

use App\Filament\Resources\Catalogues\Pages\CreateCatalogue;
use App\Filament\Resources\Catalogues\Pages\EditCatalogue;
use App\Filament\Resources\Catalogues\Pages\ListCatalogues;
use App\Filament\Resources\Catalogues\Schemas\CatalogueForm;
use App\Filament\Resources\Catalogues\Tables\CataloguesTable;
use App\Models\Catalogue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CatalogueResource extends Resource
{
    protected static ?string $model = Catalogue::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Catalogue Downloads';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Catalogue Download';

    protected static ?string $pluralModelLabel = 'Catalogue Downloads';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CatalogueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CataloguesTable::configure($table);
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
            'index' => ListCatalogues::route('/'),
            'create' => CreateCatalogue::route('/create'),
            'edit' => EditCatalogue::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
