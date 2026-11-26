<?php

namespace App\Traits;

use App\Services\ImageCompressor;
use App\Support\UploadDebug;
use Illuminate\Support\Arr;

/**
 * Automatically compresses image columns when a model is saved.
 *
 * Usage: add this trait to any Eloquent model and define a
 * $compressibleImages property listing the image attribute names.
 *
 *     use CompressesUploadedImages;
 *     protected array $compressibleImages = ['image'];
 *
 * A listed column may also hold an array — a gallery, or a structure with
 * paths nested inside it such as the products' variants. It is flattened and
 * every string is offered to the compressor, which quietly ignores anything
 * that is not an image file it can find on the disk.
 */
trait CompressesUploadedImages
{
    public static function bootCompressesUploadedImages(): void
    {
        static::saved(function ($model) {
            $columns = $model->compressibleImages ?? ['image'];
            $disk = $model->compressibleDisk ?? 'public';

            $compressor = app(ImageCompressor::class);

            foreach ($columns as $column) {
                // Only compress if the column was actually changed in this save.
                if (! $model->wasChanged($column)) {
                    continue;
                }

                $value = $model->getAttribute($column);

                if (empty($value)) {
                    continue;
                }

                foreach (Arr::flatten(Arr::wrap($value)) as $path) {
                    if (! is_string($path) || $path === '') {
                        continue;
                    }

                    // TEMPORARY diagnostics — remove with App\Support\UploadDebug.
                    UploadDebug::log('image column saved', [
                        'model' => get_class($model),
                        'id' => $model->getKey(),
                        'column' => $column,
                    ] + UploadDebug::inspect($path, $disk));

                    $bytesSaved = $compressor->compressFile($path, $disk);

                    UploadDebug::log('after compress', [
                        'path' => $path,
                        'compression_enabled' => $compressor->isEnabled(),
                        'bytes_saved' => $bytesSaved,
                    ] + UploadDebug::inspect($path, $disk));
                }
            }
        });
    }
}
