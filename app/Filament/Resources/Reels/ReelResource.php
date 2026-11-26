<?php

namespace App\Filament\Resources\Reels;

use App\Filament\Resources\Reels\Pages\CreateReel;
use App\Filament\Resources\Reels\Pages\EditReel;
use App\Filament\Resources\Reels\Pages\ListReels;
use App\Filament\Resources\Reels\Schemas\ReelForm;
use App\Filament\Resources\Reels\Tables\ReelsTable;
use App\Models\Reel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ReelResource extends Resource
{
    protected static ?string $model = Reel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'YouTube Shorts';

    protected static ?int $navigationSort = 4;

    // The table and model stay `reels` — only what the admin reads changes.
    protected static ?string $modelLabel = 'Short';

    protected static ?string $pluralModelLabel = 'YouTube Shorts';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ReelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReelsTable::configure($table);
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
            'index' => ListReels::route('/'),
            'create' => CreateReel::route('/create'),
            'edit' => EditReel::route('/{record}/edit'),
        ];
    }
}
