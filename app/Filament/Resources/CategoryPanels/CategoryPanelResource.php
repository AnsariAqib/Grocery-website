<?php

namespace App\Filament\Resources\CategoryPanels;

use App\Filament\Resources\CategoryPanels\Pages\CreateCategoryPanel;
use App\Filament\Resources\CategoryPanels\Pages\EditCategoryPanel;
use App\Filament\Resources\CategoryPanels\Pages\ListCategoryPanels;
use App\Filament\Resources\CategoryPanels\Schemas\CategoryPanelForm;
use App\Filament\Resources\CategoryPanels\Tables\CategoryPanelsTable;
use App\Models\CategoryPanel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CategoryPanelResource extends Resource
{
    protected static ?string $model = CategoryPanel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Home Page';

    protected static ?string $navigationLabel = 'Category Panels';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Category Panel';

    protected static ?string $pluralModelLabel = 'Category Panels';

    protected static ?string $recordTitleAttribute = 'heading';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryPanelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryPanelsTable::configure($table);
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
            'index' => ListCategoryPanels::route('/'),
            'create' => CreateCategoryPanel::route('/create'),
            'edit' => EditCategoryPanel::route('/{record}/edit'),
        ];
    }
}
