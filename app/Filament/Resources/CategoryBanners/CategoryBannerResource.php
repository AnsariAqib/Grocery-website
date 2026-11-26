<?php

namespace App\Filament\Resources\CategoryBanners;

use App\Filament\Resources\CategoryBanners\Pages\EditCategoryBanner;
use App\Filament\Resources\CategoryBanners\Pages\ListCategoryBanners;
use App\Filament\Resources\CategoryBanners\Schemas\CategoryBannerForm;
use App\Filament\Resources\CategoryBanners\Tables\CategoryBannersTable;
use App\Models\CategoryBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CategoryBannerResource extends Resource
{
    protected static ?string $model = CategoryBanner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Category Banners';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Category Banner';

    protected static ?string $pluralModelLabel = 'Category Banners';

    protected static ?string $recordTitleAttribute = 'title';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryBannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryBannersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoryBanners::route('/'),
            'edit' => EditCategoryBanner::route('/{record}/edit'),
        ];
    }
}
