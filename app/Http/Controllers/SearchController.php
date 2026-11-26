<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /** Products offered in the dropdown before it stops listing them. */
    private const PRODUCT_LIMIT = 7;

    /** Category shortcuts shown under the products. */
    private const CATEGORY_LIMIT = 3;

    /**
     * Type-ahead suggestions for the header search box.
     *
     * Matches the same fields the catalogue's own search does — name, brand,
     * category, variety, pack size and the admin's `search_keywords` — so a
     * word that finds a product here also finds it on the results page.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['products' => [], 'categories' => []]);
        }

        $like = '%'.$this->escapeLike($term).'%';

        $products = Product::query()
            ->active()
            ->with('subcategory.category')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('brand', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('search_keywords', 'like', $like)
                    ->orWhere('pack_size', 'like', $like)
                    ->orWhereHas('subcategory', fn ($sub) => $sub->where('name', 'like', $like)
                        ->orWhereHas('category', fn ($cat) => $cat->where('name', 'like', $like)));
            })
            ->ordered()
            ->limit(self::PRODUCT_LIMIT * 3)
            ->get()
            // A product whose category or variety was switched off should not
            // surface, same rule the catalogue page applies.
            ->filter(fn (Product $product) => $product->subcategory?->is_active
                && $product->subcategory->category?->is_active)
            ->take(self::PRODUCT_LIMIT)
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand,
                'category' => $product->subcategory?->category?->name,
                'variety' => $product->subcategory?->name,
                'image' => $product->image_url,
                // The product's own page. It used to point at the catalogue
                // filtered by the product's name, which answered a click on one
                // product with a grid of everything sharing a word with it.
                'url' => '/products/'.$product->slug,
            ])
            ->values();

        $categories = Category::query()
            ->active()
            ->where('name', 'like', $like)
            ->ordered()
            ->limit(self::CATEGORY_LIMIT)
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'icon' => $category->icon,
                'url' => '/products?category='.rawurlencode($category->name),
            ])
            ->values();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * `%`, `_` and `\` are wildcards inside LIKE — a search for "100%" would
     * otherwise match everything.
     */
    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }
}
