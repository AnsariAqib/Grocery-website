<?php

namespace App\Filament\Resources\CategoryBanners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Banner Background Image')
                ->description('Upload a high-resolution hero background image for this category page banner.')
                ->schema([
                    FileUpload::make('background_image')
                        ->label('Banner Background Image')
                        ->image()
                        ->disk('public')
                        ->directory('category-banners')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('📐 Recommended Resolution: Width 1920px × Height 550px (or 1600px × 480px, aspect ratio ~16:5). Supported: JPG, PNG, WebP (Max 25MB). When an image is uploaded, an automatic dark gradient overlay is applied so white heading text remains 100% crystal clear and readable. If left empty, the vibrant brand orange gradient is used.')
                        ->columnSpanFull(),
                ]),

            Section::make('Banner Content')
                ->description('Customize the typography, badge and call-to-action button for this page.')
                ->schema([
                    TextInput::make('type')
                        ->label('Target Page')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(fn ($record) => $record?->type_label ?? 'Category Page')
                        ->columnSpanFull(),

                    TextInput::make('badge_text')
                        ->label('Badge Text')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Retail Catalogue / Corporate Supply'),

                    TextInput::make('badge_icon')
                        ->label('Badge FontAwesome Icon')
                        ->maxLength(255)
                        ->default('fa-store')
                        ->helperText('e.g. fa-store, fa-industry, fa-boxes-stacked, fa-basket-shopping'),

                    TextInput::make('title')
                        ->label('Title Prefix')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Everything Your / Bulk Supply For'),

                    TextInput::make('highlight')
                        ->label('Title Highlight (Highlighted Text)')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Home Needs / Every Business'),

                    Textarea::make('subtitle')
                        ->label('Subtitle / Description')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Enter banner description text...'),

                    TextInput::make('cta_text')
                        ->label('Button Text')
                        ->maxLength(255)
                        ->placeholder('Browse All Products'),

                    TextInput::make('cta_url')
                        ->label('Button Link URL')
                        ->maxLength(255)
                        ->placeholder('/products'),

                    Toggle::make('is_active')
                        ->label('Banner Active')
                        ->default(true)
                        ->helperText('Enable or disable this banner on the live website.')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
