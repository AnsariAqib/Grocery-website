<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryBanner;
use App\Models\OrderStep;
use App\Models\UspCard;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class CategoriesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Categories', [
            'meta' => [
                'title' => 'All Categories | VIZORA FOODS',
                'description' => 'Explore complete in-store grocery, daily fresh produce, packaged food, dairy, home & kitchen, personal care, baby care, toys, luggage, and electronics.',
            ],
            'banner' => $this->banner(CategoryBanner::TYPE_STORE),
            'categories' => $this->categories(Category::TYPE_STORE),
            'uspCards' => $this->uspCards(UspCard::SCOPE_STORE),
        ]);
    }

    public function b2bIndex(): Response
    {
        return Inertia::render('B2bCategories', [
            'meta' => [
                'title' => 'B2B Categories | VIZORA FOODS',
                'description' => 'Explore our complete B2B commercial supply categories including bulk groceries, institutional packaging, and corporate gifting solutions.',
            ],
            'banner' => $this->banner(CategoryBanner::TYPE_B2B),
            'categories' => $this->categories(Category::TYPE_B2B),
            'uspCards' => $this->uspCards(UspCard::SCOPE_B2B),
            // Steps of the order process strip, admin-managed.
            'orderSteps' => OrderStep::active()->ordered()->get()
                ->map(fn (OrderStep $step) => [
                    'id' => $step->id,
                    'icon' => $step->icon,
                    'title' => $step->title,
                    'description' => $step->description,
                ])->values(),
        ]);
    }

    private function categories(string $type): Collection
    {
        // Keeps the catalogue on this page's side of the business, so a B2B
        // link never lands on a grid holding store products.
        $segment = $type === Category::TYPE_B2B ? 'b2b' : 'retail';

        return Category::active()
            ->ofType($type)
            ->ordered()
            ->with(['subcategories' => fn ($query) => $query->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')])
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'subCount' => $this->subCountLabel($category->subcategories->count()),
                'subCategories' => $category->subcategories->pluck('name')->values(),
                'image' => $category->image_url,
                'badgeBg' => $category->badge_bg,
                // "View All" at the top of the varieties list.
                'allHref' => '/products?segment='.$segment.'&category='.rawurlencode($category->name),
            ])->values();
    }

    private function uspCards(string $scope): Collection
    {
        return UspCard::active()->shownOn($scope)->ordered()->get()
            ->map(fn (UspCard $card) => [
                'id' => $card->id,
                'icon' => $card->icon,
                'title' => $card->title,
                'description' => $card->description,
            ])->values();
    }

    private function subCountLabel(int $count): string
    {
        return $count.' Product '.($count === 1 ? 'Variety' : 'Varieties');
    }

    private function banner(string $type): ?array
    {
        $banner = CategoryBanner::active()->ofType($type)->first();
        if (! $banner) {
            return null;
        }

        return [
            'badge' => $banner->badge_text,
            'badgeIcon' => $banner->badge_icon ?: ($type === CategoryBanner::TYPE_STORE ? 'fa-store' : 'fa-industry'),
            'title' => $banner->title,
            'highlight' => $banner->highlight,
            'subtitle' => $banner->subtitle,
            'ctaText' => $banner->cta_text ?: 'Browse All Products',
            'ctaUrl' => $this->ctaUrl($banner->cta_url, $type),
            'backgroundImage' => $banner->background_image_url,
        ];
    }

    /**
     * The banner's button has to land on this page's own side of the catalogue,
     * whether the admin left the URL blank or typed a bare "/products".
     */
    private function ctaUrl(?string $url, string $type): string
    {
        $segment = $type === CategoryBanner::TYPE_B2B ? 'b2b' : 'retail';
        $url = trim((string) $url);

        if ($url === '') {
            return '/products?segment='.$segment;
        }

        // Anything pointing elsewhere, or already scoped, is left as typed.
        if (! str_starts_with($url, '/products') || str_contains($url, 'segment=')) {
            return $url;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'segment='.$segment;
    }
}
