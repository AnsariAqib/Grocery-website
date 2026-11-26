<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Promotion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Every figure is scoped to what the public site actually shows, so the
        // label and the number agree. A plain count() here reported the hidden
        // and page-only records too, which made "Active Promotions" read 16.
        $liveProducts = Product::active()->count();
        $hiddenProducts = Product::query()->where('is_active', false)->count();

        $storeCategories = Category::active()->ofType(Category::TYPE_STORE)->count();
        $b2bCategories = Category::active()->ofType(Category::TYPE_B2B)->count();

        $homePromotions = Promotion::active()->onHome()->count();
        $pagePromotions = Promotion::active()->onPage()->count();

        $newInquiries = Inquiry::query()->where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Live products', $liveProducts)
                ->description($hiddenProducts > 0
                    ? $hiddenProducts.' hidden from the catalogue'
                    : 'All products visible')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($hiddenProducts > 0 ? 'warning' : 'success'),

            Stat::make('Categories', $storeCategories + $b2bCategories)
                ->description($storeCategories.' store · '.$b2bCategories.' B2B')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary'),

            Stat::make('Live promotions', $homePromotions + $pagePromotions)
                ->description($homePromotions.' on home · '.$pagePromotions.' on offers page')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('primary'),

            Stat::make('Inquiries this week', $newInquiries)
                ->description($newInquiries > 0 ? 'Last 7 days' : 'None in the last 7 days')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($newInquiries > 0 ? 'success' : 'gray'),
        ];
    }
}
