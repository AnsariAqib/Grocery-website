<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Product extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    // `variants` is listed so the photos nested inside it are compressed too;
    // its other values are not file paths and the compressor skips them.
    protected array $compressibleImages = ['image', 'images', 'variants'];

    /** The card's slider holds three photos; anything past that is ignored. */
    public const MAX_IMAGES = 3;

    protected $fillable = [
        'subcategory_id', 'name', 'slug', 'sku', 'brand', 'description', 'search_keywords',
        'image', 'images', 'price', 'price_on_request', 'pack_size', 'variants',
        'badge', 'badge_bg', 'sort_order', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price_on_request' => 'boolean',
        'images' => 'array',
        'variants' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $product) {
            // Once the gallery is filled it is the source of truth, but the
            // single `image` column is kept in step: the home page rail and the
            // content-health widget both read it directly.
            $first = collect(Arr::wrap($product->images))
                ->first(fn ($path) => is_string($path) && $path !== '');

            if ($first !== null) {
                $product->image = $first;
            }
        });
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        // The gallery's first photo is the product's face; `image` is what the
        // older content and the home page rail still carry.
        return $this->image_urls[0] ?? $this->mediaUrl($this->image);
    }

    /**
     * The product's own photos, resolved to browsable URLs.
     *
     * @return array<int, string>
     */
    public function getImageUrlsAttribute(): array
    {
        $urls = $this->resolveImages($this->images);

        if ($urls !== []) {
            return $urls;
        }

        $legacy = $this->mediaUrl($this->image);

        return $legacy ? [$legacy] : [];
    }

    /**
     * Pack sizes for the card's chips, each with the price it switches to.
     *
     * A variant that carries no photos of its own falls back to the product's,
     * so switching size never empties the slider.
     *
     * @return array<int, array<string, mixed>>
     */
    public function variantCards(): array
    {
        $productImages = $this->image_urls;
        $cards = [];

        foreach ((array) $this->variants as $variant) {
            if (! is_array($variant) || blank($variant['size'] ?? null)) {
                continue;
            }

            // A variant switched off in the admin is not offered, but the
            // product itself stays on the shelf.
            if (array_key_exists('is_active', $variant) && ! $variant['is_active']) {
                continue;
            }

            $own = $this->resolveImages($variant['images'] ?? null);

            $cards[] = [
                'sku' => $variant['sku'] ?? null,
                'size' => $variant['size'],
                'price' => $variant['price'] ?? null,
                'priceOnRequest' => (bool) ($variant['price_on_request'] ?? false),
                'images' => $own !== [] ? $own : $productImages,
            ];
        }

        return $cards;
    }

    /**
     * @param  mixed  $paths
     * @return array<int, string>
     */
    private function resolveImages($paths): array
    {
        $urls = [];

        foreach ((array) $paths as $path) {
            if (! is_string($path)) {
                continue;
            }

            $url = $this->mediaUrl($path);

            if ($url !== null) {
                $urls[] = $url;
            }
        }

        return array_slice(array_values(array_unique($urls)), 0, self::MAX_IMAGES);
    }

    /**
     * The flat shape Pages/Products.vue consumes. Kept identical to the
     * previously hardcoded arrays so the catalogue filters and variety pills
     * keep working unchanged.
     */
    public function toCardArray(): array
    {
        $variants = $this->variantCards();

        // With variants the first one is what the card opens on, so the flat
        // price/pack fields describe it. That keeps every existing consumer —
        // the home page rail, the price filter, the sorts — working unchanged.
        $default = $variants[0] ?? null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            // Every card links to the product's own page.
            'href' => '/products/'.$this->slug,
            'category' => $this->subcategory?->category?->name,
            'variety' => $this->subcategory?->name,
            'brand' => $this->brand,
            'description' => $this->description,
            'keywords' => $this->search_keywords,
            'image' => $this->image_url,
            'images' => $this->image_urls,
            'variants' => $variants,
            // `price` stays the display string the card prints; `priceOnRequest`
            // tells the card to print "Price on Request" instead, and keeps the
            // product out of price-range filtering and price sorting.
            'price' => $default ? $default['price'] : $this->price,
            'priceOnRequest' => $default ? $default['priceOnRequest'] : (bool) $this->price_on_request,
            'packSize' => $default ? $default['size'] : $this->pack_size,
            'badge' => $this->badge,
            'badgeBg' => $this->badge_bg,
            // Which inquiry type the "Ask Now" form should preselect.
            'segment' => $this->subcategory?->category?->type === Category::TYPE_B2B ? 'b2b' : 'retail',
        ];
    }
}
