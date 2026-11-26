<?php

namespace App\Filament\Resources\Logos;

use App\Filament\Resources\Logos\Pages\CreateLogo;
use App\Filament\Resources\Logos\Pages\EditLogo;
use App\Filament\Resources\Logos\Pages\ListLogos;
use App\Filament\Resources\Logos\Schemas\LogoForm;
use App\Filament\Resources\Logos\Tables\LogosTable;
use App\Models\Logo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LogoResource extends Resource
{
    protected static ?string $model = Logo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Client & Brand Logos';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Logo';

    protected static ?string $pluralModelLabel = 'Client & Brand Logos';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LogoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogosTable::configure($table);
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
            'index' => ListLogos::route('/'),
            'create' => CreateLogo::route('/create'),
            'edit' => EditLogo::route('/{record}/edit'),
        ];
    }
}
