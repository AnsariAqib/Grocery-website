<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Promotion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The gaps that make the public site look unfinished. Each tile turns red only
 * when there is something to fix, so a healthy panel reads as all-green.
 */
class ContentHealth extends BaseWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Content health';

    protected ?string $description = 'Things a visitor would notice as missing.';

    protected function getStats(): array
    {
        $emptyCategories = Category::query()
            ->where('type', Category::TYPE_STORE)
            ->where('is_active', true)
            ->whereDoesntHave('subcategories.products', fn ($query) => $query->where('products.is_active', true))
            ->count();

        $productsWithoutImage = Product::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('image')->orWhere('image', ''))
            ->count();

        $homeSlides = HeroSlide::active()->count();

        $homePromotions = Promotion::active()->onHome()->count();

        return [
            Stat::make('Categories with no products', $emptyCategories)
                ->description($emptyCategories > 0
                    ? 'These show an empty grid on the catalogue'
                    : 'Every live category has products')
                ->descriptionIcon($emptyCategories > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($emptyCategories > 0 ? 'danger' : 'success'),

            Stat::make('Products with no image', $productsWithoutImage)
                ->description($productsWithoutImage > 0
                    ? 'Cards render with a blank thumbnail'
                    : 'All product cards have artwork')
                ->descriptionIcon($productsWithoutImage > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($productsWithoutImage > 0 ? 'danger' : 'success'),

            Stat::make('Hero slides', $homeSlides)
                ->description($homeSlides === 0
                    ? 'The homepage hero is empty'
                    : 'Rotating on the homepage')
                ->descriptionIcon($homeSlides === 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($homeSlides === 0 ? 'danger' : 'success'),

            // The home section is built as a four-card grid; fewer than four
            // leaves a visible hole in the row.
            Stat::make('Home promotion cards', $homePromotions)
                ->description(match (true) {
                    $homePromotions === 0 => 'The Promotion section is hidden',
                    $homePromotions < 4 => 'The four-card row is incomplete',
                    default => 'Full row of four',
                })
                ->descriptionIcon($homePromotions < 4 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($homePromotions < 4 ? 'warning' : 'success'),
        ];
    }
}
