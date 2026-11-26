<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolves a media column to a browsable URL.
 *
 * Content seeded from the original hardcoded arrays holds ready-made paths
 * ("/assets/logo.png") or remote URLs (the Unsplash hero images); anything
 * uploaded through the admin holds a path relative to the public disk. Both
 * have to work side by side, so seeded values are passed through untouched.
 */
trait HasMediaUrl
{
    public function mediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
            || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
