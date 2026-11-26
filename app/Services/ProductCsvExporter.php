<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The catalogue back out to a spreadsheet.
 *
 * Deliberately the exact mirror of {@see ProductImporter}: same headings, same
 * order, same way of writing a price, a status and a photo name. A sheet that
 * comes out of here can be edited in Excel and fed straight back into Bulk
 * Product Import, which matches on SKU and updates rather than duplicates.
 *
 * The sheet's shape is one row per pack size, tied together by Parent SKU — so
 * a product with three sizes writes three rows, the first of which carries the
 * product's own details (brand, badge, keywords, description).
 *
 * Two things the sheet cannot carry, both of them limits of the format rather
 * than of this export:
 *
 * - Photos travel by filename, not by file. A photo that is not in the product
 *   image folder — the older seeded `/assets/...` artwork, for one — is named
 *   in the sheet but reported as missing when the sheet is imported.
 * - A product whose own SKU matches none of its sizes cannot be written as
 *   such: the format gives the product the code of its first size. Products
 *   with no code at all are fine — the slug stands in as the Parent SKU to
 *   keep their rows together, and the importer matches them by name.
 */
class ProductCsvExporter
{
    /**
     * Stream the catalogue as a CSV download.
     *
     * Streamed and chunked rather than assembled in memory: a bulk-imported
     * catalogue runs to thousands of rows, and the panel's PHP worker would
     * otherwise hold every one of them for the length of the request.
     *
     * @param  Collection<int, Product>|null  $records  null exports everything
     */
    public function download(?Collection $records = null): StreamedResponse
    {
        $filename = 'products-'.now()->format('Y-m-d-Hi').'.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // Excel reads a bare UTF-8 file as Windows-1252, which turns every
            // ₹ in the price column into gibberish; the BOM is what tells it
            // otherwise.
            fwrite($handle, "\xEF\xBB\xBF");
            $this->put($handle, array_keys(ProductImporter::COLUMNS));

            $write = function (Product $product) use ($handle) {
                foreach ($this->rowsFor($product) as $row) {
                    $this->put($handle, array_map(
                        fn (string $field) => $row[$field] ?? '',
                        array_values(ProductImporter::COLUMNS),
                    ));
                }
            };

            if ($records !== null) {
                $records->loadMissing('subcategory.category')->each($write);
            } else {
                Product::query()
                    ->with('subcategory.category')
                    // Grouped the way the catalogue reads, so the sheet opens
                    // in the same order as the site.
                    ->orderBy('subcategory_id')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->chunk(200, fn (Collection $chunk) => $chunk->each($write));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * One row per pack size, or a single row for a product sold in one size.
     *
     * @return array<int, array<string, string>>
     */
    private function rowsFor(Product $product): array
    {
        $category = $product->subcategory?->category;

        $shared = [
            // Every row repeats these: the importer reads them off one row, but
            // a person scrolling the sheet should not have to look upwards to
            // see what a row belongs to.
            'parent_sku' => $this->parentSku($product),
            'category' => (string) ($category?->name ?? ''),
            'subcategory' => (string) ($product->subcategory?->name ?? ''),
            'section' => $category?->type === Category::TYPE_B2B ? 'B2B' : 'Store',
            'name' => (string) $product->name,
        ];

        // Only on the first row — these belong to the product, not to a size,
        // and repeating them would suggest they could differ per size.
        $details = [
            'brand' => (string) ($product->brand ?? ''),
            'badge' => (string) ($product->badge ?? ''),
            'badge_bg' => (string) ($product->badge_bg ?? ''),
            'search_keywords' => (string) ($product->search_keywords ?? ''),
            'description' => (string) ($product->description ?? ''),
        ];

        $variants = $this->orderedVariants($product);

        if ($variants === []) {
            return [array_merge($shared, $details, [
                'sku' => (string) ($product->sku ?? ''),
                'pack_size' => (string) ($product->pack_size ?? ''),
                'price_on_request' => $product->price_on_request ? 'Yes' : '',
                'price' => $product->price_on_request ? '' : (string) ($product->price ?? ''),
                'status' => $product->is_active ? 'publish' : 'hidden',
            ], $this->imageCells($product->images, $product->image))];
        }

        $rows = [];

        foreach ($variants as $index => $variant) {
            $onRequest = (bool) ($variant['price_on_request'] ?? false);
            // A size left switched off inside a live product is written as
            // hidden; a hidden product hides all of its rows.
            $live = $product->is_active
                && (! array_key_exists('is_active', $variant) || $variant['is_active']);

            $row = array_merge($shared, $index === 0 ? $details : [], [
                'sku' => (string) ($variant['sku'] ?? ''),
                'pack_size' => (string) ($variant['size'] ?? ''),
                'price_on_request' => $onRequest ? 'Yes' : '',
                'price' => $onRequest ? '' : (string) ($variant['price'] ?? ''),
                'status' => $live ? 'publish' : 'hidden',
                // A size with no photos of its own falls back to the product's on
                // the storefront, and an empty cell says the same thing on import.
            ], $this->imageCells($variant['images'] ?? null));

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * The product's sizes, with the one that carries the product's own SKU
     * first.
     *
     * The importer treats the row whose SKU equals the Parent SKU as the one
     * describing the product, and falls back to the first row when no row
     * matches — putting that size first makes both paths land on the same row.
     *
     * @return array<int, array<string, mixed>>
     */
    private function orderedVariants(Product $product): array
    {
        $variants = array_values(array_filter(
            (array) $product->variants,
            fn ($variant) => is_array($variant) && filled($variant['size'] ?? null),
        ));

        if ($variants === [] || blank($product->sku)) {
            return $variants;
        }

        foreach ($variants as $index => $variant) {
            if ($index > 0 && ($variant['sku'] ?? null) === $product->sku) {
                $match = array_splice($variants, $index, 1);
                array_unshift($variants, $match[0]);
                break;
            }
        }

        return $variants;
    }

    /**
     * What ties a product's rows together.
     *
     * The SKU where there is one. Where there is not, the slug stands in: the
     * cell cannot be left empty, because every blank Parent SKU in the sheet
     * would group into a single imaginary product with dozens of sizes.
     */
    private function parentSku(Product $product): string
    {
        return (string) ($product->sku ?: $product->slug);
    }

    /**
     * The Image and Sub Image cells for a set of stored photo paths.
     *
     * Only the filename travels — that is what the importer looks up in the
     * product image folder, and what the Bulk Image Upload page names its
     * files by.
     *
     * @param  mixed  $paths
     * @return array{image: string, sub_image: string}
     */
    private function imageCells($paths, ?string $legacy = null): array
    {
        $names = [];

        foreach ((array) $paths as $path) {
            if (is_string($path) && $path !== '') {
                $names[] = basename($path);
            }
        }

        // Older rows carry a single `image` and an empty gallery.
        if ($names === [] && filled($legacy)) {
            $names[] = basename($legacy);
        }

        $names = array_slice(array_values(array_unique($names)), 0, Product::MAX_IMAGES);

        return [
            'image' => $names[0] ?? '',
            // The importer splits this cell on either separator; a semicolon
            // keeps the sheet readable in a single Excel column.
            'sub_image' => implode('; ', array_slice($names, 1)),
        ];
    }

    /**
     * @param  resource  $handle
     * @param  array<int, string>  $row
     */
    private function put($handle, array $row): void
    {
        // PHP 8.4 deprecates the two-argument call; '' keeps plain RFC 4180
        // quoting, which is what Excel and the importer's reader both expect.
        fputcsv($handle, $row, ',', '"', '');
    }
}
