<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * The shared naming rules for the product photo folder.
 *
 * Photos arrive twice over: dropped into the admin's bulk uploader, and named
 * again in the import sheet ("images/product/2020/12/30003.jpg"). The two only
 * meet if both sides reduce a filename the same way, so that reduction lives
 * here rather than in either caller.
 */
class ProductImageLibrary
{
    public const DIRECTORY = 'products';

    /**
     * A filename as it is stored: lowercase and hyphenated.
     *
     * Uploads keep their given names so the sheet can point at them, but the
     * given names are not always safe — this project's own assets already
     * carry spaces and uppercase extensions, which work on Windows and break
     * on a Linux host.
     */
    public static function normalise(string $filename): string
    {
        // A sheet may hold a whole path; only the last segment names the file.
        $filename = basename(str_replace('\\', '/', trim($filename)));

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $name = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) ?: 'image';

        return $extension === '' ? $name : "{$name}.{$extension}";
    }

    /**
     * The stored path for a reference out of the sheet, or null when no such
     * photo has been uploaded yet. Returning null rather than a broken path
     * lets the importer report the miss instead of writing it into a product.
     */
    public static function resolve(?string $reference): ?string
    {
        if (blank($reference)) {
            return null;
        }

        $path = self::DIRECTORY.'/'.self::normalise($reference);

        return Storage::disk('public')->exists($path) ? $path : null;
    }

    /**
     * Several references at once — a sheet's main photo plus its sub images.
     * Misses are dropped, so the caller gets only paths that exist.
     *
     * @param  array<int, string|null>  $references
     * @return array<int, string>
     */
    public static function resolveMany(array $references, int $limit = 3): array
    {
        $paths = [];

        foreach ($references as $reference) {
            $path = self::resolve($reference);

            if ($path !== null && ! in_array($path, $paths, true)) {
                $paths[] = $path;
            }
        }

        return array_slice($paths, 0, $limit);
    }

    /**
     * A slice of the folder, newest first, ready to render as thumbnails.
     *
     * The whole folder is listed to sort it — there is no ordered listing on a
     * plain disk — but each file is asked for its date exactly once, because a
     * comparison-time lookup costs a stat per comparison and this folder grows
     * to hundreds of photos.
     *
     * @return array<int, array{name: string, url: string}>
     */
    public static function page(int $limit, int $offset = 0): array
    {
        $disk = Storage::disk('public');

        $dated = [];

        foreach ($disk->files(self::DIRECTORY) as $path) {
            $dated[] = [$path, $disk->lastModified($path)];
        }

        usort($dated, fn (array $a, array $b) => $b[1] <=> $a[1]);

        return array_map(
            fn (array $entry) => [
                'name' => basename($entry[0]),
                'url' => $disk->url($entry[0]),
            ],
            array_slice($dated, $offset, $limit),
        );
    }

    public static function count(): int
    {
        return count(Storage::disk('public')->files(self::DIRECTORY));
    }
}
