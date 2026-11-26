<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * TEMPORARY — diagnostics for uploaded images that 404 on the server.
 *
 * Writes to storage/logs/uploads.log through an ad-hoc channel so the trace is
 * a clean file on its own rather than buried in laravel.log. Delete this class
 * and its callers in CompressesUploadedImages / ImageCompressor once uploads
 * are confirmed working.
 */
class UploadDebug
{
    public static function path(): string
    {
        return storage_path('logs/uploads.log');
    }

    public static function log(string $event, array $context = []): void
    {
        Log::build([
            'driver' => 'single',
            'path' => self::path(),
            'level' => 'debug',
        ])->info($event, $context);
    }

    /**
     * Last lines of the log, for hosts where there is no shell to `tail` with.
     */
    public static function tail(int $lines = 200): string
    {
        if (! is_file(self::path())) {
            return '';
        }

        $content = trim((string) file_get_contents(self::path()));

        if ($content === '') {
            return '';
        }

        return implode(PHP_EOL, array_slice(preg_split("/\r\n|\n/", $content), -$lines));
    }

    public static function clear(): void
    {
        if (is_file(self::path())) {
            @unlink(self::path());
        }
    }

    /**
     * Everything needed to tell "the file was never written" apart from "the
     * file is on disk but /storage does not point at it".
     */
    public static function inspect(string $path, string $disk = 'public'): array
    {
        $storage = Storage::disk($disk);
        $full = $storage->path($path);

        // public/storage is the storage:link symlink the web server reads from.
        $link = public_path('storage');
        $throughLink = $link.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);

        return [
            'path' => $path,
            'disk' => $disk,
            'disk_root' => $storage->path(''),
            'file_full_path' => $full,
            'file_exists' => file_exists($full),
            'file_size' => file_exists($full) ? filesize($full) : null,
            'file_readable' => file_exists($full) ? is_readable($full) : null,
            'file_perms' => file_exists($full) ? substr(sprintf('%o', fileperms($full)), -4) : null,
            'dir_perms' => is_dir(dirname($full)) ? substr(sprintf('%o', fileperms(dirname($full))), -4) : null,
            'public_storage' => $link,
            'public_storage_exists' => file_exists($link),
            'public_storage_is_link' => is_link($link),
            'public_storage_target' => is_link($link) ? readlink($link) : null,
            // The one that matters: can the web server reach the file at all?
            'reachable_through_public_storage' => file_exists($throughLink),
            'url' => $storage->url($path),
            'disk_url_prefix' => config('filesystems.disks.'.$disk.'.url'),
            'app_url' => config('app.url'),
            'process_user' => function_exists('posix_geteuid') && function_exists('posix_getpwuid')
                ? (posix_getpwuid(posix_geteuid())['name'] ?? null)
                : get_current_user(),
        ];
    }
}
