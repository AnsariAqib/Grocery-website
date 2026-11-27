<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ProductsController extends Controller
{
    /** ?segment= values the category pages deep-link with, mapped to category types. */
    private const SEGMENTS = [
        'retail' => Category::TYPE_STORE,
        'store' => Category::TYPE_STORE,
        'b2b' => Category::TYPE_B2B,
    ];

    public function index(Request $request): Response
    {
        // Both category pages send visitors here, so without a segment the grid
        // would answer "Show all B2B products" with the store catalogue too.
        $type = self::SEGMENTS[strtolower((string) $request->query('segment'))] ?? null;

        $products = Product::active()
            ->with('subcategory.category')
            ->ordered()
            ->get()
            // Products whose category or variety was deactivated should not surface.
            ->filter(fn (Product $product) => $product->subcategory?->is_active
                && $product->subcategory->category?->is_active
                && ($type === null || $product->subcategory->category->type === $type))
            ->map(fn (Product $product) => $product->toCardArray())
            ->values();

        return Inertia::render('Products', [
            'meta' => $this->meta($request),
            'products' => $products,
            // Drives the top-level filter pills, so it follows the admin's order.
            'categoryList' => $this->categoryNames($type),
            // Every category's varieties, keyed by category name. Taken from the
            // catalogue rather than from the products so the B2B side — which has
            // categories and varieties but no products yet — still gets a full
            // pill bar. Sent as a map because picking a category pill filters
            // client-side, with no round trip to look its varieties up.
            'varietiesByCategory' => $this->varietiesByCategory($type),
            // Lets the page keep the "All categories" link visible on a segment
            // view that has no category picked yet.
            'segment' => $type === Category::TYPE_B2B ? 'b2b' : ($type === Category::TYPE_STORE ? 'retail' : ''),
            'initialCategory' => $request->query('category', 'all'),
            'initialVariety' => $request->query('variety', ''),
            // Set by the navbar search box.
            'initialSearch' => (string) $request->query('search', ''),
            // Brand names for the navbar's search suggestions and the advanced
            // brand filter, in one alphabetical list.
            'brandList' => $this->brandNames(),
            // Both category pages deep-link here, so "All Categories" has to go
            // back to whichever one the visitor actually came from.
            'backHref' => $this->backHref($type, $request->query('category')),
        ]);
    }

    /**
     * The catalogue's title and description, narrowed to whatever was asked
     * for.
     *
     * Both category pages deep-link here as /products?category=…&variety=…, so
     * these are real landing pages — "Cooking Oil in Grocery" deserves to be
     * findable as that, not as one more copy of the whole price list.
     *
     * @return array<string, string>
     */
    private function meta(Request $request): array
    {
        $category = trim((string) $request->query('category'));
        $variety = trim((string) $request->query('variety'));

        if (strtolower($category) === 'all') {
            $category = '';
        }

        if (filled($variety) && filled($category)) {
            return [
                'title' => "{$variety} in {$category} | VIZORA FOODS",
                'description' => "Browse our {$variety} range under {$category} — brands, pack sizes and prices stocked at Jay Tulsi Agency & Vinayak Stores, Surat. Retail and bulk B2B supply.",
            ];
        }

        if (filled($category)) {
            return [
                'title' => "{$category} Products & Price List | VIZORA FOODS",
                'description' => "Complete {$category} range with brands, pack sizes and prices at Jay Tulsi Agency & Vinayak Stores, Surat — for homes, retailers and bulk business supply.",
            ];
        }

        return [
            'title' => 'Product Catalogue & Price List | VIZORA FOODS',
            'description' => 'Explore complete product price list across supermarket grocery, dairy, packaged food, home cleaning, stationery, and electronics in Surat.',
        ];
    }

    /**
     * Every brand on the shelf, one name per entry.
     *
     * A product carried under more than one label is typed into the sheet's
     * single Brand column as "Amul / Mother Dairy / Hocco". Those are three
     * brands, and the filter lists them as three — so the cell is split here
     * the same way {@see resources/js/Pages/Products.vue} splits it.
     */
    private function brandNames(): Collection
    {
        return Product::active()
            ->whereNotNull('brand')
            ->distinct()
            ->pluck('brand')
            ->flatMap(fn (string $brand) => preg_split('/\s*[\/|;,]\s*/', $brand) ?: [])
            ->map(fn (string $brand) => trim($brand))
            ->filter()
            // Case-insensitive, so a sheet that types "AMUL" once and "Amul"
            // twice does not put the same brand on the list twice.
            ->unique(fn (string $brand) => mb_strtolower($brand))
            ->sort(fn (string $a, string $b) => strcasecmp($a, $b))
            ->values();
    }

    /** Category names in admin order, store side first when both are on the page. */
    private function categoryNames(?string $type): Collection
    {
        return Category::active()
            ->when($type, fn ($query) => $query->ofType($type))
            ->ordered()
            ->get()
            ->sortBy(fn (Category $category) => $category->type === Category::TYPE_B2B ? 1 : 0)
            ->pluck('name')
            ->values();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function varietiesByCategory(?string $type): array
    {
        return Category::active()
            ->when($type, fn ($query) => $query->ofType($type))
            ->ordered()
            ->with(['subcategories' => fn ($query) => $query->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')])
            ->get()
            ->mapWithKeys(fn (Category $category) => [
                $category->name => $category->subcategories->pluck('name')->all(),
            ])
            ->all();
    }

    private function backHref(?string $type, ?string $category): string
    {
        if ($type !== null) {
            return $type === Category::TYPE_B2B ? '/b2b-categories' : '/categories';
        }

        if (blank($category)) {
            return '/categories';
        }

        // Older links carry only ?category=, so the side has to be looked up.
        $isB2b = Category::ofType(Category::TYPE_B2B)
            ->where('name', $category)
            ->exists();

        return $isB2b ? '/b2b-categories' : '/categories';
    }
}
