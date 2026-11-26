<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

/**
 * One product on a page of its own.
 *
 * The catalogue grid can only show so much — a card has room for a name, a
 * price and one line of description. This is where the rest of it goes: every
 * photo, every pack size with its own rate, the full description, the codes,
 * and an enquiry button that arrives at the form already knowing what is being
 * asked about.
 *
 * It is also the page a search engine can rank: /products is one URL for the
 * whole shelf, while this gives each product its own.
 */
class ProductController extends Controller
{
    /** How many other products from the same variety are offered underneath. */
    private const RELATED = 8;

    public function show(string $slug): Response
    {
        $product = Product::query()
            ->with('subcategory.category')
            ->where('slug', $slug)
            ->firstOrFail();

        // A product whose category or variety was switched off is off the site,
        // not merely off the grid — the same rule the catalogue page applies.
        abort_unless(
            $product->is_active
                && $product->subcategory?->is_active
                && $product->subcategory->category?->is_active,
            404
        );

        $category = $product->subcategory->category;
        $isB2b = $category->type === Category::TYPE_B2B;
        $segment = $isB2b ? 'b2b' : 'retail';

        $card = $product->toCardArray();

        return Inertia::render('Product', [
            'meta' => [
                'title' => $product->name.' | '.($product->brand ? $product->brand.' — ' : '').'Jay Tulsi Agency & Vinayak Stores',
                'description' => $this->description($product, $card),
                // What WhatsApp and Facebook show when the page is shared: the
                // product, not the shop's logo.
                'image' => $card['image'],
            ],
            'product' => $card + [
                'sku' => $product->sku,
                'keywords' => $product->search_keywords,
                'segment' => $segment,
            ],
            // The trail the page prints, and what the "back to" link points at.
            'trail' => [
                'segment' => $segment,
                'categoryLabel' => $isB2b ? 'B2B Categories' : 'Store Categories',
                'categoryHref' => $isB2b ? '/b2b-categories' : '/categories',
                'category' => $category->name,
                'categoryProductsHref' => '/products?segment='.$segment.'&category='.rawurlencode($category->name),
                'variety' => $product->subcategory->name,
                'varietyHref' => '/products?segment='.$segment
                    .'&category='.rawurlencode($category->name)
                    .'&variety='.rawurlencode($product->subcategory->name),
            ],
            'related' => $this->related($product),
        ]);
    }

    /**
     * The meta description: the admin's own line where there is one, otherwise
     * a sentence built from what the product actually is.
     *
     * @param  array<string, mixed>  $card
     */
    private function description(Product $product, array $card): string
    {
        if (filled($product->description)) {
            return $product->description;
        }

        $sizes = collect($card['variants'])->pluck('size')->filter()->implode(', ');

        return trim(collect([
            $product->name,
            $product->brand ? 'by '.$product->brand : null,
            'at Jay Tulsi Agency & Vinayak Stores, Surat',
            $sizes ? '— available in '.$sizes : ($product->pack_size ? '— '.$product->pack_size : null),
            '. Retail and bulk B2B supply.',
        ])->filter()->implode(' '));
    }

    /**
     * Others from the same variety, so a visitor who is not sold on this one
     * has somewhere to go that is not the back button.
     *
     * @return array<int, array<string, mixed>>
     */
    private function related(Product $product): array
    {
        $items = Product::query()
            ->active()
            ->where('subcategory_id', $product->subcategory_id)
            ->whereKeyNot($product->getKey())
            ->with('subcategory.category')
            ->ordered()
            ->limit(self::RELATED)
            ->get();

        // Fill up to 4 items from the same category, then any active product so
        // "You May Also Like" never disappears on a single-item variety.
        if ($items->count() < 4 && $product->subcategory?->category_id) {
            $exclude = $items->pluck('id')->push($product->getKey())->all();
            $fromCategory = Product::query()
                ->active()
                ->whereHas('subcategory', fn ($q) => $q->where('category_id', $product->subcategory->category_id))
                ->whereNotIn('id', $exclude)
                ->with('subcategory.category')
                ->ordered()
                ->limit(self::RELATED - $items->count())
                ->get();

            $items = $items->concat($fromCategory);
        }

        if ($items->count() < 4) {
            $exclude = $items->pluck('id')->push($product->getKey())->all();
            $fallback = Product::query()
                ->active()
                ->whereNotIn('id', $exclude)
                ->with('subcategory.category')
                ->ordered()
                ->limit(self::RELATED - $items->count())
                ->get();

            $items = $items->concat($fallback);
        }

        return $items
            ->map(fn (Product $other) => $other->toCardArray())
            ->values()
            ->all();
    }
}
