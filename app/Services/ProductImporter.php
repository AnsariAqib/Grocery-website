<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\Csv\Reader;
use RuntimeException;

/**
 * Loads the supplier's catalogue export into the site.
 *
 * The sheet holds one row per pack size, tied together by Parent SKU: the row
 * whose SKU equals its own Parent SKU describes the product, the rest are its
 * other sizes. Photos are not in the sheet, only named — they have to be on
 * the disk already, which is what the admin's Bulk Image Upload page is for.
 *
 * Both the `products:import` command and the admin's Bulk Product Import page
 * run through here, so a sheet behaves identically whichever way it is fed in.
 */
class ProductImporter
{
    /**
     * The sheet's headings mapped to what they mean.
     *
     * Written the way a person would type them: these are also the headings of
     * the template the admin page hands out, and what its help text lists. A
     * sheet's own headings are matched against these loosely — see
     * {@see self::normaliseHeading()} — so capitals and spacing do not matter.
     */
    public const COLUMNS = [
        'SKU' => 'sku',
        'Parent SKU' => 'parent_sku',
        'Category' => 'category',
        'Sub Category' => 'subcategory',
        'Section' => 'section',
        'Name' => 'name',
        'Image' => 'image',
        'Sub Image' => 'sub_image',
        'Brand' => 'brand',
        'Pack Size' => 'pack_size',
        'Price On Request' => 'price_on_request',
        'Sale Price' => 'price',
        'Badge' => 'badge',
        'Badge Color' => 'badge_bg',
        'Search Keywords' => 'search_keywords',
        'Status' => 'status',
        'Product Short Desc' => 'description',
    ];

    /** The three a row cannot be imported without. */
    public const REQUIRED = ['Name', 'Category', 'Sub Category'];

    /** @var array<string, int> */
    private array $stats = [
        'products_created' => 0,
        'products_updated' => 0,
        'variants' => 0,
        'rows_skipped' => 0,
    ];

    /** How many products the result names before it stops listing them. */
    public const MAX_LISTED = 250;

    /** @var array<int, string> */
    private array $warnings = [];

    /** @var array<int, array{name: string, sku: string|null, created: bool, sizes: int}> */
    private array $products = [];

    /** @var array<string, true> */
    private array $createdCategories = [];

    /** @var array<string, true> */
    private array $missingImages = [];

    /** @var (callable(int, int): void)|null */
    private $onProgress = null;

    /**
     * Called after each product, with the number done and the total. The
     * command draws a progress bar from it; the page ignores it.
     *
     * @param  (callable(int, int): void)|null  $callback
     */
    public function trackProgress(?callable $callback): self
    {
        $this->onProgress = $callback;

        return $this;
    }

    /**
     * @throws RuntimeException when the file is unreadable or holds no rows
     */
    public function import(string $path, bool $dryRun = false): ProductImportResult
    {
        $this->reset();

        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("No such file: {$path}");
        }

        $rows = $this->read($path);

        if ($rows === []) {
            throw new RuntimeException('The sheet has no usable rows. Check that the first line holds the column headings.');
        }

        $groups = $this->groupByParent($rows);
        $done = 0;

        DB::beginTransaction();

        try {
            foreach ($groups as $group) {
                $this->importGroup($group);

                $done++;

                if ($this->onProgress !== null) {
                    ($this->onProgress)($done, count($groups));
                }
            }

            // A dry run walks the whole sheet so its report is exact, then
            // throws the writes away.
            $dryRun ? DB::rollBack() : DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return new ProductImportResult(
            dryRun: $dryRun,
            rows: count($rows),
            groups: count($groups),
            productsCreated: $this->stats['products_created'],
            productsUpdated: $this->stats['products_updated'],
            variants: $this->stats['variants'],
            rowsSkipped: $this->stats['rows_skipped'],
            createdCategories: array_keys($this->createdCategories),
            missingImages: array_keys($this->missingImages),
            warnings: $this->warnings,
            products: $this->products,
        );
    }

    private function reset(): void
    {
        $this->stats = [
            'products_created' => 0,
            'products_updated' => 0,
            'variants' => 0,
            'rows_skipped' => 0,
        ];
        $this->warnings = [];
        $this->createdCategories = [];
        $this->missingImages = [];
        $this->products = [];
    }

    /**
     * How a warning names the row it is about: the line in the file first,
     * because that is what the admin has to scroll to, then whatever the row
     * calls itself.
     *
     * @param  array<string, mixed>  $row
     */
    private static function where(array $row): string
    {
        $line = isset($row['__line']) ? 'Row '.$row['__line'] : 'A row';

        $name = filled($row['name'] ?? null)
            ? ' ("'.$row['name'].'")'
            : (filled($row['sku'] ?? null) ? ' (SKU '.$row['sku'].')' : '');

        return $line.$name;
    }

    /**
     * A cell as valid UTF-8.
     *
     * Excel on Windows writes CSV in Windows-1252 unless it is told otherwise,
     * so "6 kg/cm²", a degree sign or a curly quote arrives as a single byte
     * that MySQL refuses outright — "Incorrect string value: '\xB2…'". The whole
     * sheet imports inside one transaction, so that one byte in one description
     * rolls back every row of the file and the import looks like it did
     * nothing. Converting here is the difference between a sheet that works and
     * a sheet that has to be re-saved as UTF-8 by hand.
     *
     * Windows-1252 rather than ISO-8859-1 because it is what Excel actually
     * writes: the two agree on the accented letters and differ exactly on the
     * smart quotes and dashes Word and Excel like to insert.
     */
    public static function utf8(string $value): string
    {
        if ($value === '' || mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        return mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
    }

    /**
     * A heading reduced to the form both sides are compared in: "Sub Category",
     * "SUB_CATEGORY" and "subcategory" all land on the same key.
     */
    public static function normaliseHeading(string $heading): string
    {
        return (string) preg_replace('/[^a-z0-9]/', '', strtolower($heading));
    }

    /**
     * {@see self::COLUMNS} keyed the way a sheet's headings will be matched.
     *
     * @return array<string, string>
     */
    private static function headingMap(): array
    {
        $map = [];

        foreach (self::COLUMNS as $heading => $field) {
            $map[self::normaliseHeading($heading)] = $field;
        }

        return $map;
    }

    /**
     * A one-row template for the admin to fill in, so nobody has to guess the
     * headings. The example is a two-size product, since that is the shape most
     * likely to be got wrong: both rows share a Parent SKU, and the row whose
     * SKU matches it carries the product's own details.
     */
    public static function sampleCsv(): string
    {
        $rows = [
            array_keys(self::COLUMNS),
            [
                'JT-OIL-500', 'JT-OIL-500', 'Grocery', 'Cooking Oil', 'Store',
                'Groundnut Oil', 'groundnut-oil.jpg', 'groundnut-oil-back.jpg',
                'Jay Tulsi', '500 ml', '', '193', 'BESTSELLER', '#FA5400',
                'oil, cooking oil, groundnut', 'publish', 'Cold-pressed, sealed at the mill.',
            ],
            [
                'JT-OIL-1L', 'JT-OIL-500', 'Grocery', 'Cooking Oil', 'Store',
                'Groundnut Oil', 'groundnut-oil-1l.jpg', '',
                '', '1 Litre', '', '349', '', '',
                '', 'publish', '',
            ],
        ];

        $csv = '';

        foreach ($rows as $row) {
            $csv .= implode(',', array_map(
                // Excel needs a quoted field the moment one holds a comma.
                fn (string $cell) => '"'.str_replace('"', '""', $cell).'"',
                $row,
            )).PHP_EOL;
        }

        return $csv;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function read(string $path): array
    {
        $csv = Reader::from($path, 'r');
        $csv->setHeaderOffset(0);
        // Excel writes a BOM in front of the first heading, which would
        // otherwise make "SKU" unmatchable.
        $csv->skipInputBOM();

        $rows = [];

        $map = self::headingMap();

        // A heading nobody recognises is the quietest way for a sheet to fail:
        // every row reads as empty and the report says nothing at all. Said out
        // loud once, at the top.
        $unknown = [];

        foreach ($csv->getHeader() as $heading) {
            if (filled($heading) && ! isset($map[self::normaliseHeading((string) $heading)])) {
                $unknown[] = trim((string) $heading);
            }
        }

        if ($unknown !== []) {
            $this->warnings[] = 'Ignored '.count($unknown).' column'.(count($unknown) === 1 ? '' : 's')
                .' the importer does not know: '.implode(', ', array_slice($unknown, 0, 8))
                .(count($unknown) > 8 ? '...' : '').'. Check the spelling against "Download template".';
        }

        // The record's own offset, so every later complaint can name the line
        // the admin has to go and look at. With a header row the offsets count
        // it too, so the first product row is offset 1 and line 2.
        foreach ($csv->getRecords() as $offset => $record) {
            $row = ['__line' => $offset + 1];

            foreach ($record as $heading => $value) {
                $key = $map[self::normaliseHeading((string) $heading)] ?? null;

                if ($key !== null) {
                    $row[$key] = trim(self::utf8((string) $value));
                }
            }

            // A row with neither a code nor a name is a spacer, not a product.
            if (blank($row['sku'] ?? null) && blank($row['name'] ?? null)) {
                continue;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     * @return array<string, array<int, array<string, string>>>
     */
    private function groupByParent(array $rows): array
    {
        $groups = [];

        foreach ($rows as $row) {
            // A row with no parent stands on its own.
            $parent = filled($row['parent_sku'] ?? null) ? $row['parent_sku'] : ($row['sku'] ?? '');
            $groups[$parent][] = $row;
        }

        return $groups;
    }

    /**
     * @param  array<int, array<string, string>>  $group
     */
    private function importGroup(array $group): void
    {
        // The row that describes the product, rather than one of its sizes.
        $parent = null;

        foreach ($group as $row) {
            if (($row['sku'] ?? null) === ($row['parent_sku'] ?? null)) {
                $parent = $row;
                break;
            }
        }

        $parent ??= $group[0];

        if (blank($parent['name'] ?? null)) {
            $this->stats['rows_skipped'] += count($group);
            $this->warnings[] = self::where($parent).': skipped, because the Name column is empty and a product cannot be made without a name.';

            return;
        }

        $subcategory = $this->subcategoryFor($parent);

        if ($subcategory === null) {
            $this->stats['rows_skipped'] += count($group);

            return;
        }

        $sku = $parent['sku'] ?? null;
        $product = filled($sku) ? Product::firstWhere('sku', $sku) : null;

        // A sheet with no codes in it — a catalogue export of products added by
        // hand, or a supplier list that never had any — is matched on the name
        // inside the same variety instead, so importing it twice updates the
        // same products rather than making a second copy of each. Only products
        // that carry no code of their own are matched this way; one that does
        // is left to be found by it.
        if ($product === null && blank($sku)) {
            $product = Product::query()
                ->where('subcategory_id', $subcategory->id)
                ->where('name', $parent['name'])
                ->where(fn ($query) => $query->whereNull('sku')->orWhere('sku', ''))
                ->first();
        }

        $isNew = $product === null;
        $product ??= new Product;

        $product->fill([
            'subcategory_id' => $subcategory->id,
            'name' => $parent['name'],
            'sku' => $sku ?: null,
            'brand' => $this->text($parent['brand'] ?? null),
            'description' => $this->text($parent['description'] ?? null),
            'search_keywords' => $this->text($parent['search_keywords'] ?? null),
            'badge' => $this->text($parent['badge'] ?? null),
            'badge_bg' => $this->text($parent['badge_bg'] ?? null),
            'images' => $this->imagesFor($parent),
            'is_active' => $this->isPublished($parent),
        ]);

        $product->slug ??= $this->uniqueSlug($parent['name'], $sku);

        // A single-size product keeps its size and price on the product itself,
        // so its card shows a plain pack line rather than a lone chip.
        if (count($group) === 1) {
            $product->pack_size = $this->text($parent['pack_size'] ?? null);
            $product->price = $this->price($parent['price'] ?? null);
            $product->price_on_request = $this->isPriceOnRequest($parent);
            $product->variants = null;
        } else {
            $product->variants = $this->variantsFor($group);
            $this->stats['variants'] += count($product->variants);
        }

        $product->save();

        $this->stats[$isNew ? 'products_created' : 'products_updated']++;

        // Named so the admin page can show what actually landed, rather than
        // only a count. Long catalogues stop listing past MAX_LISTED.
        if (count($this->products) < self::MAX_LISTED) {
            $this->products[] = [
                'name' => $parent['name'],
                'sku' => $sku ?: null,
                'created' => $isNew,
                'sizes' => count((array) $product->variants),
            ];
        }
    }

    /**
     * @param  array<int, array<string, string>>  $group
     * @return array<int, array<string, mixed>>
     */
    private function variantsFor(array $group): array
    {
        $variants = [];

        foreach ($group as $row) {
            if (blank($row['pack_size'] ?? null)) {
                $this->warnings[] = self::where($row).': left out, because it shares a Parent SKU with other rows - which makes it one of the pack sizes - and its Pack Size column is empty.';
                $this->stats['rows_skipped']++;

                continue;
            }

            $variants[] = [
                'sku' => $row['sku'] ?: null,
                'size' => $row['pack_size'],
                'price' => $this->price($row['price'] ?? null),
                'price_on_request' => $this->isPriceOnRequest($row),
                'images' => $this->imagesFor($row),
                'is_active' => $this->isPublished($row),
            ];
        }

        return $variants;
    }

    /**
     * The row's main photo followed by its sub images, kept only where the
     * file has actually been uploaded.
     *
     * @param  array<string, string>  $row
     * @return array<int, string>
     */
    private function imagesFor(array $row): array
    {
        $references = array_merge(
            [$row['image'] ?? null],
            // Sub images arrive semicolon-separated in one cell.
            preg_split('/[;,]/', (string) ($row['sub_image'] ?? '')) ?: [],
        );

        $references = array_values(array_filter(array_map('trim', $references)));

        $resolved = ProductImageLibrary::resolveMany($references, Product::MAX_IMAGES);

        foreach ($references as $reference) {
            if (ProductImageLibrary::resolve($reference) === null) {
                $this->missingImages[ProductImageLibrary::normalise($reference)] = true;
            }
        }

        return $resolved;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function subcategoryFor(array $row): ?Subcategory
    {
        $categoryName = $row['category'] ?? '';
        $subcategoryName = $row['subcategory'] ?? '';

        if (blank($categoryName) || blank($subcategoryName)) {
            $missing = blank($categoryName) && blank($subcategoryName)
                ? 'both Category and Sub Category are empty'
                : (blank($categoryName) ? 'the Category column is empty' : 'the Sub Category column is empty');

            $this->warnings[] = self::where($row).': skipped, because '.$missing.'. Every product needs both.';

            return null;
        }

        $type = str_contains(strtolower($row['section'] ?? ''), 'b2b')
            ? Category::TYPE_B2B
            : Category::TYPE_STORE;

        $category = Category::where('name', $categoryName)->where('type', $type)->first();

        if ($category === null) {
            $category = Category::create([
                'name' => $categoryName,
                'slug' => $this->uniqueSlug($categoryName, null, Category::class),
                'type' => $type,
                'sort_order' => (int) Category::where('type', $type)->max('sort_order') + 1,
                'is_active' => true,
            ]);

            $this->createdCategories[$categoryName.' ('.$type.')'] = true;
        }

        $subcategory = Subcategory::where('category_id', $category->id)
            ->where('name', $subcategoryName)
            ->first();

        return $subcategory ?? Subcategory::create([
            'category_id' => $category->id,
            'name' => $subcategoryName,
            'slug' => $this->uniqueSlug($categoryName.' '.$subcategoryName, null, Subcategory::class),
            'sort_order' => (int) Subcategory::where('category_id', $category->id)->max('sort_order') + 1,
            'is_active' => true,
        ]);
    }

    /**
     * Prices arrive bare ("193"); the site prints whatever string it is given,
     * so the symbol is added here. Anything already formatted is left alone.
     */
    private function price(?string $raw): ?string
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return null;
        }

        if (! is_numeric($raw)) {
            return $raw;
        }

        $value = (float) $raw;

        return '₹'.($value == (int) $value
            ? number_format($value, 0, '.', ',')
            : number_format($value, 2, '.', ','));
    }

    /**
     * A blank cell is nothing, not an empty string — the panel's own fields
     * leave these columns null, and a sheet should not write '' over them.
     */
    private function text(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function isPriceOnRequest(array $row): bool
    {
        return in_array(strtolower(trim($row['price_on_request'] ?? '')), ['y', 'yes', '1', 'true'], true);
    }

    /**
     * @param  array<string, string>  $row
     */
    private function isPublished(array $row): bool
    {
        $status = strtolower(trim($row['status'] ?? ''));

        // A blank status is taken as published — most exports only mark the
        // rows that are held back.
        return $status === '' || in_array($status, ['publish', 'published', 'active', '1'], true);
    }

    private function uniqueSlug(string $source, ?string $suffix = null, string $model = Product::class): string
    {
        $base = Str::slug($source) ?: 'item';
        $slug = $base;

        if ($model::where('slug', $slug)->exists() && filled($suffix)) {
            $slug = $base.'-'.Str::slug($suffix);
        }

        $attempt = 2;

        while ($model::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$attempt++;
        }

        return $slug;
    }
}
