<?php

namespace App\Services;

/**
 * What one run of the importer did, in a shape both callers can render:
 * the console command prints it as a table, the admin page as cards.
 */
final class ProductImportResult
{
    /**
     * @param  array<int, string>  $createdCategories
     * @param  array<int, string>  $missingImages
     * @param  array<int, string>  $warnings
     * @param  array<int, array{name: string, sku: string|null, created: bool, sizes: int}>  $products
     */
    public function __construct(
        public readonly bool $dryRun,
        public readonly int $rows,
        public readonly int $groups,
        public readonly int $productsCreated,
        public readonly int $productsUpdated,
        public readonly int $variants,
        public readonly int $rowsSkipped,
        public readonly array $createdCategories,
        public readonly array $missingImages,
        public readonly array $warnings,
        public readonly array $products = [],
    ) {}

    public function productsTouched(): int
    {
        return $this->productsCreated + $this->productsUpdated;
    }

    /**
     * Livewire can only carry primitives between requests, so the admin page
     * holds the run as an array and rebuilds nothing.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'dryRun' => $this->dryRun,
            'rows' => $this->rows,
            'groups' => $this->groups,
            'productsCreated' => $this->productsCreated,
            'productsUpdated' => $this->productsUpdated,
            'variants' => $this->variants,
            'rowsSkipped' => $this->rowsSkipped,
            'createdCategories' => $this->createdCategories,
            'missingImages' => $this->missingImages,
            'warnings' => $this->warnings,
            'products' => $this->products,
            'hasProblems' => $this->hasProblems(),
        ];
    }

    /**
     * Whether the run is worth a second look before committing — nothing here
     * blocks an import, but each one means some rows will land incomplete.
     */
    public function hasProblems(): bool
    {
        return $this->missingImages !== [] || $this->warnings !== [] || $this->rowsSkipped > 0;
    }
}
