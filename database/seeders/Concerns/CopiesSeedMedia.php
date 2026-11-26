<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Moves seeded artwork onto the public disk so the admin's upload fields can
 * actually show and replace it.
 *
 * Without this, a field seeded with "/assets/grocery.PNG" renders as an empty
 * uploader, because Filament looks the path up on the public disk. Copying once
 * at seed time means every media column holds a real public-disk path and the
 * uploads behave the same for seeded and newly added records.
 *
 * Filenames are normalised on the way in, which also retires the spaces and
 * uppercase extensions in the original asset names ("fruits and veggies.png",
 * "grocery.PNG") that would break on a case-sensitive host.
 */
trait CopiesSeedMedia
{
    protected function seedMedia(?string $source, string $directory): ?string
    {
        if (blank($source)) {
            return null;
        }

        $target = $directory.'/'.$this->normaliseFilename($source);

        if (Storage::disk('public')->exists($target)) {
            return $target;
        }

        $contents = str_starts_with($source, 'http')
            ? $this->fetchRemote($source)
            : $this->readLocal($source);

        if ($contents === null) {
            // Nothing copied: keep the original value. HasMediaUrl passes
            // absolute paths and URLs straight through, so the site still shows
            // the image even though the upload field starts empty.
            return $source;
        }

        Storage::disk('public')->put($target, $contents);

        return $target;
    }

    private function normaliseFilename(string $source): string
    {
        $path = parse_url($source, PHP_URL_PATH) ?: $source;
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION)) ?: 'png';
        $name = Str::slug(pathinfo($path, PATHINFO_FILENAME)) ?: Str::random(8);

        return $name.'.'.$extension;
    }

    private function readLocal(string $source): ?string
    {
        $path = public_path(ltrim($source, '/'));

        return is_file($path) ? file_get_contents($path) : null;
    }

    private function fetchRemote(string $url): ?string
    {
        $context = stream_context_create([
            'http' => ['timeout' => 15, 'user_agent' => 'JayTulsiSeeder/1.0'],
        ]);

        $contents = @file_get_contents($url, false, $context);

        return $contents === false ? null : $contents;
    }
}
