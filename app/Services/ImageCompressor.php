<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\UploadDebug;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageCompressor
{
    /**
     * Directories inside storage/app/public that hold uploaded images.
     */
    private const UPLOAD_DIRS = [
        'hero',
        'products',
        'categories',
        'logos',
        'panels',
        'promotions',
        'stores',
        'certificates',
        'reels',
        'leaders',
        'category-banners',
        'popups',
    ];

    /**
     * Extensions we can safely compress via GD.
     */
    private const COMPRESSIBLE = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Files smaller than this (bytes) are not worth re-encoding.
     */
    private const MIN_SIZE = 10 * 1024; // 10 KB

    /**
     * Compress a single file on the given disk.
     *
     * @return int Bytes saved (0 if skipped / not compressible).
     */
    public function compressFile(string $path, string $disk = 'public'): int
    {
        if (! $this->isEnabled()) {
            return 0;
        }

        $storage = Storage::disk($disk);

        if (! $storage->exists($path)) {
            return 0;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, self::COMPRESSIBLE, true)) {
            return 0;
        }

        $originalSize = $storage->size($path);

        if ($originalSize < self::MIN_SIZE) {
            return 0;
        }

        try {
            // Temporarily raise memory for GD — large PNGs can need 100MB+ in RAM.
            $previousMemory = ini_get('memory_limit');
            ini_set('memory_limit', '512M');

            $quality = $this->quality();
            $fullPath = $storage->path($path);

            $manager = new ImageManager(new GdDriver);
            $image = $manager->decode($fullPath);

            // Scale down huge images to a reasonable web size to save space
            if ($image->width() > 1600) {
                $image->scale(width: 1600);
            }

            if (in_array($extension, ['jpg', 'jpeg'], true)) {
                $encoded = $image->encode(new JpegEncoder(quality: $quality));
            } elseif ($extension === 'webp') {
                $encoded = $image->encode(new WebpEncoder(quality: $quality));
            } else {
                // PNG: re-encode with interlacing.
                // Note: PNG is lossless. True size reduction comes from the scaling above.
                $encoded = $image->encode(new PngEncoder(interlaced: true));
            }

            $encodedString = (string) $encoded;
            $newSize = strlen($encodedString);

            // Only keep the compressed version if it's actually smaller.
            if ($newSize >= $originalSize) {
                return 0;
            }

            if (file_put_contents($fullPath, $encodedString) === false) {
                // A failed write here would otherwise be reported as a saving.
                UploadDebug::log('compress write failed', [
                    'path' => $path,
                    'full_path' => $fullPath,
                    'writable' => is_writable($fullPath),
                ]);

                return 0;
            }

            return $originalSize - $newSize;
        } catch (\Throwable $e) {
            Log::warning("ImageCompressor: failed to compress [{$path}]", [
                'error' => $e->getMessage(),
            ]);

            UploadDebug::log('compress threw', [
                'path' => $path,
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            return 0;
        } finally {
            // Restore original memory limit
            ini_set('memory_limit', $previousMemory ?: '128M');
        }
    }

    /**
     * Compress every image across all upload directories.
     *
     * @return array{compressed: int, skipped: int, bytes_saved: int}
     */
    public function compressBulk(): array
    {
        $stats = ['compressed' => 0, 'skipped' => 0, 'bytes_saved' => 0];

        if (! $this->isEnabled()) {
            return $stats;
        }

        $storage = Storage::disk('public');

        foreach (self::UPLOAD_DIRS as $dir) {
            if (! $storage->exists($dir)) {
                continue;
            }

            $files = $storage->files($dir);

            foreach ($files as $file) {
                $saved = $this->compressFile($file);

                if ($saved > 0) {
                    $stats['compressed']++;
                    $stats['bytes_saved'] += $saved;
                } else {
                    $stats['skipped']++;
                }
            }
        }

        return $stats;
    }

    public function isEnabled(): bool
    {
        return Setting::bool('image_compression_enabled', true);
    }

    public function quality(): int
    {
        $q = (int) Setting::val('image_compression_quality', 82);

        return max(1, min(100, $q));
    }
}
