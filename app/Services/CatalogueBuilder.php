<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

/**
 * Builds a printed catalogue out of the live catalogue.
 *
 * The site already knows every product, every pack size and every price — this
 * turns that into the PDF the sales desk sends out, so nobody has to keep a
 * separate price list in Word and remember to update both.
 *
 * What goes in is chosen in the admin (see ProductCataloguePage): which side of
 * the business, which categories, and whether prices and codes are printed —
 * a customer copy usually wants prices, a shelf list usually wants codes.
 */
class CatalogueBuilder
{
    private ?ImageManager $images = null;

    /** microtime() after which no new thumbnail is made; null for no limit. */
    private ?float $thumbnailDeadline = null;

    /** Products printed without a photo because the time budget ran out. */
    private int $pendingThumbnails = 0;

    /**
     * Cap the time spent shrinking photos in this run.
     *
     * Shared hosting answers a long request with a gateway timeout, and the
     * first run on a big catalogue has hundreds of photos to shrink. Past the
     * budget, products without a cached thumbnail print without one; every
     * thumbnail already made is kept, so the next run picks up where this one
     * stopped and the photos fill in after a click or two.
     */
    public function limitThumbnailTime(float $seconds): static
    {
        $this->thumbnailDeadline = microtime(true) + $seconds;
        $this->pendingThumbnails = 0;

        return $this;
    }

    public function pendingThumbnails(): int
    {
        return $this->pendingThumbnails;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function pdf(array $options, ?array $data = null): \Barryvdh\DomPDF\PDF
    {
        // A caller that already built the data (to count products first)
        // passes it in, so the photos are not processed a second time.
        return Pdf::loadView('pdf.catalogue', $data ?? $this->data($options))
            ->setPaper('a4')
            ->setOption(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']);
    }

    /**
     * A filename that says what is inside without being opened.
     *
     * @param  array<string, mixed>  $options
     */
    public function filename(array $options): string
    {
        $side = match ($options['segment'] ?? 'all') {
            Category::TYPE_B2B => 'b2b',
            Category::TYPE_STORE => 'store',
            default => 'full',
        };

        return 'jay-tulsi-'.$side.'-catalogue-'.now()->format('Y-m-d').'.pdf';
    }

    /**
     * Everything the template prints.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function data(array $options): array
    {
        $settings = Setting::map();
        $groups = $this->groups($options);

        $productCount = collect($groups)->sum(fn (array $group) => count($group['items']));

        return [
            'title' => trim((string) ($options['title'] ?? '')) ?: 'Product Catalogue',
            'subtitle' => trim((string) ($options['subtitle'] ?? '')),
            'kicker' => match ($options['segment'] ?? 'all') {
                Category::TYPE_B2B => 'B2B & Corporate Supply',
                Category::TYPE_STORE => 'Retail & Supermarket',
                default => 'Complete Product Range',
            },
            'groups' => $groups,
            'productCount' => $productCount,
            'categoryCount' => count($groups),
            'showPrices' => (bool) ($options['show_prices'] ?? true),
            'showSku' => (bool) ($options['show_sku'] ?? false),
            'showDescriptions' => (bool) ($options['show_descriptions'] ?? true),
            'showImages' => (bool) ($options['show_images'] ?? true),
            'photosPending' => $this->pendingThumbnails,
            'printedOn' => now()->format('d M Y'),
            // dompdf reads the logo off the disk, not over HTTP — a URL would
            // need remote fetching turned on, which is a needless risk here.
            'logo' => $this->logoPath(),
            'business' => $settings['business_legal_name'] ?? 'Jay Tulsi Agency',
            'address' => $settings['address_full'] ?? 'Surat, Gujarat, India',
            'phone' => Setting::phoneDisplay(),
            'email' => $settings['email_primary'] ?? '',
            'hours' => $settings['store_hours'] ?? '',
        ];
    }

    /**
     * Products grouped the way the catalogue reads: category, then variety.
     *
     * @param  array<string, mixed>  $options
     * @return array<int, array{name: string, items: array<int, array<string, mixed>>, subgroups: array<int, array{name: string, items: array<int, array<string, mixed>>}>}>
     */
    private function groups(array $options): array
    {
        $segment = $options['segment'] ?? 'all';
        $categoryIds = array_filter((array) ($options['categories'] ?? []));

        $products = Product::query()
            ->when(! ($options['include_hidden'] ?? false), fn ($query) => $query->active())
            ->with('subcategory.category')
            ->ordered()
            ->get()
            ->filter(function (Product $product) use ($segment, $categoryIds) {
                $category = $product->subcategory?->category;

                if (! $category || ! $product->subcategory?->is_active || ! $category->is_active) {
                    return false;
                }

                if ($segment !== 'all' && $category->type !== $segment) {
                    return false;
                }

                return $categoryIds === [] || in_array($category->id, $categoryIds);
            });

        $withImages = (bool) ($options['show_images'] ?? true);

        return $products
            ->groupBy(fn (Product $product) => $product->subcategory->category->name)
            ->map(fn (Collection $items, string $name) => [
                'name' => $name,
                'items' => $items->map(fn (Product $product) => $this->row($product))->all(),
                'subgroups' => $items
                    ->groupBy(fn (Product $product) => $product->subcategory->name)
                    ->map(fn (Collection $subItems, string $subName) => [
                        'name' => $subName,
                        'items' => $subItems->map(fn (Product $product) => $this->row($product, $withImages))->values()->all(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * One printed line.
     *
     * A product sold in several sizes prints the first size in the pack column
     * and lists the rest under the name, rather than repeating the product for
     * every size — the sheet is read by a person, not imported.
     *
     * @return array<string, mixed>
     */
    private function row(Product $product, bool $withImage = false): array
    {
        $variants = array_values(array_filter(
            (array) $product->variants,
            fn ($variant) => is_array($variant) && filled($variant['size'] ?? null)
                && (! array_key_exists('is_active', $variant) || $variant['is_active']),
        ));

        $first = $variants[0] ?? null;

        return [
            'sku' => (string) ($first['sku'] ?? $product->sku ?? ''),
            'name' => $product->name,
            'brand' => (string) ($product->brand ?? ''),
            'description' => (string) ($product->description ?? ''),
            'pack' => (string) ($first['size'] ?? $product->pack_size ?? ''),
            'price' => (string) ($first ? ($first['price'] ?? '') : ($product->price ?? '')),
            'onRequest' => $first
                ? (bool) ($first['price_on_request'] ?? false) || blank($first['price'] ?? null)
                : (bool) $product->price_on_request || blank($product->price),
            'sizes' => array_map(fn (array $variant) => (string) $variant['size'], $variants),
            'image' => $withImage ? $this->thumbnail($product) : null,
        ];
    }

    /**
     * The product's first photo as a small JPEG thumbnail dompdf can open.
     *
     * Uploads are full-size and often WebP or AVIF, which dompdf either cannot
     * read or would embed at full weight. Each one is shrunk once and kept in
     * storage/app/catalogue-thumbs, keyed on the source file's path, size and
     * mtime — decoding a few hundred full-size photos on every click is what
     * pushed the request past the server's gateway timeout. A replaced photo
     * gets a new key, so a stale thumbnail is never printed. Remote URLs are
     * skipped for the same reason the logo is read off the disk.
     */
    public function thumbnail(Product $product): ?string
    {
        $path = collect(Arr::wrap($product->images))
            ->first(fn ($value) => is_string($value) && $value !== '') ?? $product->image;

        if (blank($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        $file = str_starts_with($path, '/')
            ? public_path(ltrim($path, '/'))
            : Storage::disk('public')->path($path);

        if (! is_file($file)) {
            return null;
        }

        $thumb = storage_path('app/catalogue-thumbs/'.sha1($file.'|'.filesize($file).'|'.filemtime($file)).'.jpg');

        if (is_file($thumb)) {
            return $thumb;
        }

        if ($this->thumbnailDeadline !== null && microtime(true) > $this->thumbnailDeadline) {
            $this->pendingThumbnails++;

            return null;
        }

        try {
            $this->images ??= new ImageManager(new GdDriver);

            $encoded = $this->images->decode($file)
                ->scaleDown(width: 120, height: 120)
                ->encode(new JpegEncoder(quality: 72));

            if (! is_dir(dirname($thumb))) {
                mkdir(dirname($thumb), 0755, true);
            }

            file_put_contents($thumb, (string) $encoded);

            return $thumb;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * The logo as a path dompdf can open. Null rather than a broken image when
     * the file is not where it is expected.
     */
    private function logoPath(): ?string
    {
        $path = public_path('assets/logo.png');

        return is_file($path) ? $path : null;
    }
}
