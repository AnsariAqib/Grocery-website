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
            ->first();

        if (! $product) {
            return $this->showShowcase($slug);
        }

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
                'title' => $product->name.' | '.($product->brand ? $product->brand.' — ' : '').'VIZORA FOODS',
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

    /**
     * Fallback for homepage showcase products (Featured, Best Sellers, New Arrivals)
     * or cards passing product details via query parameters.
     */
    private function showShowcase(string $slug): Response
    {
        $catalog = [
            'fresh-vegetables' => ['name' => 'Fresh Vegetables', 'unit' => '1 kg', 'rating' => '4.8', 'reviews' => '2,426', 'price' => 149, 'oldPrice' => 179, 'off' => '17% OFF', 'image' => '/assets/feat-1.jpg', 'category' => 'Vegetables'],
            'organic-milk' => ['name' => 'Organic Milk', 'unit' => '1 L', 'rating' => '4.7', 'reviews' => '1,920', 'price' => 79, 'oldPrice' => 89, 'off' => '11% OFF', 'image' => '/assets/feat-2.jpg', 'category' => 'Dairy & Eggs'],
            'grocery-essentials' => ['name' => 'Grocery Essentials', 'unit' => '500 g', 'rating' => '4.5', 'reviews' => '1,348', 'price' => 149, 'oldPrice' => 169, 'off' => '12% OFF', 'image' => '/assets/feat-3.jpg', 'category' => 'Daily Staples'],
            'daily-snacks-pack' => ['name' => 'Daily Snacks Pack', 'unit' => '400 g', 'rating' => '4.6', 'reviews' => '1,072', 'price' => 229, 'oldPrice' => 259, 'off' => '12% OFF', 'image' => '/assets/feat-4.jpg', 'category' => 'Snacks'],
            'dry-fruits-mix' => ['name' => 'Dry Fruits Mix', 'unit' => '250 g', 'rating' => '4.7', 'reviews' => '892', 'price' => 249, 'oldPrice' => 299, 'off' => '17% OFF', 'image' => '/assets/feat-5.jpg', 'category' => 'Dry Fruits'],
            'fresh-fruits' => ['name' => 'Fresh Fruits', 'unit' => '1 kg', 'rating' => '4.5', 'reviews' => '1,560', 'price' => 179, 'oldPrice' => 199, 'off' => '10% OFF', 'image' => '/assets/feat-6.jpg', 'category' => 'Fruits'],
            'fresh-red-apples' => ['name' => 'Fresh Red Apples', 'unit' => '1 kg', 'rating' => '4.6', 'reviews' => '1,248', 'price' => 149, 'oldPrice' => 209, 'off' => '29% OFF', 'image' => '/assets/feat-6.jpg', 'category' => 'Fruits'],
            'fresh-bananas' => ['name' => 'Fresh Bananas', 'unit' => '1 kg', 'rating' => '4.5', 'reviews' => '862', 'price' => 59, 'oldPrice' => 79, 'off' => '23% OFF', 'image' => '/assets/feat-6.jpg', 'category' => 'Fruits'],
            'fresh-tomatoes' => ['name' => 'Fresh Tomatoes', 'unit' => '1 kg', 'rating' => '4.7', 'reviews' => '1,356', 'price' => 39, 'oldPrice' => 54, 'off' => '28% OFF', 'image' => '/assets/feat-1.jpg', 'category' => 'Vegetables'],
            'fresh-potatoes' => ['name' => 'Fresh Potatoes', 'unit' => '2 kg', 'rating' => '4.5', 'reviews' => '983', 'price' => 69, 'oldPrice' => 99, 'off' => '29% OFF', 'image' => '/assets/feat-3.jpg', 'category' => 'Vegetables'],
            'onions' => ['name' => 'Onions', 'unit' => '1 kg', 'rating' => '4.6', 'reviews' => '1,142', 'price' => 45, 'oldPrice' => 65, 'off' => '30% OFF', 'image' => '/assets/feat-1.jpg', 'category' => 'Vegetables'],
            'fresh-carrots' => ['name' => 'Fresh Carrots', 'unit' => '1 kg', 'rating' => '4.5', 'reviews' => '768', 'price' => 55, 'oldPrice' => 74, 'off' => '25% OFF', 'image' => '/assets/feat-5.jpg', 'category' => 'Vegetables'],
            'cold-pressed-mustard-oil' => ['name' => 'Cold-Pressed Mustard Oil', 'unit' => '1 L', 'rating' => '4.8', 'reviews' => '312', 'price' => 215, 'oldPrice' => 260, 'off' => '17% OFF', 'image' => '/assets/feat-3.jpg', 'category' => 'Oils & Ghee'],
            'raw-forest-honey' => ['name' => 'Raw Forest Honey', 'unit' => '500 g', 'rating' => '4.9', 'reviews' => '428', 'price' => 349, 'oldPrice' => 420, 'off' => '17% OFF', 'image' => '/assets/feat-5.jpg', 'category' => 'Pantry'],
            'organic-brown-chana' => ['name' => 'Organic Brown Chana', 'unit' => '1 kg', 'rating' => '4.6', 'reviews' => '245', 'price' => 115, 'oldPrice' => 140, 'off' => '18% OFF', 'image' => '/assets/feat-4.jpg', 'category' => 'Pulses & Grains'],
            'a2-cow-ghee' => ['name' => 'A2 Cow Ghee', 'unit' => '500 ml', 'rating' => '4.9', 'reviews' => '519', 'price' => 599, 'oldPrice' => 720, 'off' => '17% OFF', 'image' => '/assets/feat-2.jpg', 'category' => 'Dairy & Eggs'],
            'himalayan-pink-salt' => ['name' => 'Himalayan Pink Salt', 'unit' => '1 kg', 'rating' => '4.7', 'reviews' => '384', 'price' => 89, 'oldPrice' => 115, 'off' => '23% OFF', 'image' => '/assets/store-categories-products.png', 'category' => 'Spices & Salt'],
            'seasonal-fruit-box' => ['name' => 'Seasonal Fruit Box', 'unit' => '3 kg', 'rating' => '4.8', 'reviews' => '276', 'price' => 299, 'oldPrice' => 375, 'off' => '20% OFF', 'image' => '/assets/feat-1.jpg', 'category' => 'Fruits'],
        ];

        $req = request();
        $entry = $catalog[$slug] ?? null;
        $queryName = trim((string) $req->query('name', ''));

        if (! $entry && $queryName === '') {
            abort(404);
        }

        $name = $queryName !== '' ? $queryName : $entry['name'];
        $image = trim((string) $req->query('image', '')) ?: ($entry['image'] ?? '/assets/feat-1.jpg');
        $unit = trim((string) $req->query('unit', '')) ?: ($entry['unit'] ?? '1 kg');
        $price = trim((string) $req->query('price', '')) ?: (string) ($entry['price'] ?? '149');
        $rating = trim((string) $req->query('rating', '')) ?: ($entry['rating'] ?? '4.8');
        $reviews = trim((string) $req->query('reviews', '')) ?: ($entry['reviews'] ?? '1,248');
        $off = trim((string) $req->query('off', '')) ?: ($entry['off'] ?? 'Farm Fresh');
        $categoryName = $entry['category'] ?? 'Fresh Groceries';

        $formattedPrice = str_starts_with($price, '₹') ? $price : '₹'.$price;

        $related = collect($catalog)
            ->reject(fn ($_, $key) => $key === $slug)
            ->take(4)
            ->map(fn ($item, $key) => [
                'id' => $key,
                'name' => $item['name'],
                'brand' => 'VIZORA FOODS',
                'image' => $item['image'],
                'price' => '₹'.$item['price'],
                'priceOnRequest' => false,
                'href' => '/products/'.$key,
            ])
            ->values()
            ->all();

        return Inertia::render('Product', [
            'meta' => [
                'title' => $name.' | VIZORA FOODS',
                'description' => $name.' ('.$unit.') — sourced fresh from local farms and morning mandis at VIZORA FOODS.',
                'image' => $image,
            ],
            'product' => [
                'id' => $slug,
                'slug' => $slug,
                'name' => $name,
                'brand' => 'VIZORA FOODS',
                'badge' => $off,
                'image' => $image,
                'images' => [$image],
                'price' => $formattedPrice,
                'priceOnRequest' => false,
                'packSize' => $unit,
                'rating' => $rating,
                'reviews' => $reviews,
                'description' => $name.' ('.$unit.') — handpicked fresh every morning from local farms and regional mandis, hygienically packed and delivered straight to your doorstep by VIZORA FOODS.',
                'variants' => [
                    [
                        'size' => $unit,
                        'price' => $formattedPrice,
                        'priceOnRequest' => false,
                        'sku' => strtoupper(substr(str_replace('-', '', $slug), 0, 8)),
                    ],
                ],
                'sku' => strtoupper(substr(str_replace('-', '', $slug), 0, 8)),
                'segment' => 'retail',
            ],
            'trail' => [
                'segment' => 'retail',
                'categoryLabel' => 'All Categories',
                'categoryHref' => '/categories',
                'category' => $categoryName,
                'categoryProductsHref' => '/products',
                'variety' => 'Fresh Essentials',
                'varietyHref' => '/products',
            ],
            'related' => $related,
        ]);
    }
}
