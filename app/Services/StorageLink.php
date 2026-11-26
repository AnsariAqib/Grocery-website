<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Health check and repair for the public/storage symlink.
 *
 * `php artisan storage:link` normally creates it, but on a host with no shell
 * access there is no way to run that — and no way to notice it is missing
 * either, because uploads save perfectly well and only 404 when the browser
 * asks for them back. This exposes both the diagnosis and the fix to the admin
 * panel (see App\Filament\Pages\StorageDiagnosticsPage).
 */
class StorageLink
{
    public function linkPath(): string
    {
        return public_path('storage');
    }

    public function targetPath(): string
    {
        return storage_path('app/public');
    }

    public function status(): array
    {
        $link = $this->linkPath();
        $target = $this->targetPath();
        $resolved = realpath($link);

        return [
            'link_path' => $link,
            'target_path' => $target,
            'exists' => file_exists($link) || is_link($link),
            'is_link' => is_link($link),
            'link_target' => is_link($link) ? readlink($link) : null,
            // The failure mode on this project: the repo checked out a real
            // directory where the symlink belongs.
            'is_real_directory' => file_exists($link) && ! is_link($link) && is_dir($link),
            'resolves_to_target' => $resolved !== false && $resolved === realpath($target),
            'target_exists' => is_dir($target),
            'target_writable' => is_dir($target) && is_writable($target),
            'public_writable' => is_writable(dirname($link)),
            'symlink_supported' => $this->symlinkSupported(),
            'reachable' => $this->probe(),
            'php_user' => $this->phpUser(),
        ];
    }

    public function isHealthy(): bool
    {
        $status = $this->status();

        return $status['reachable'] && $status['resolves_to_target'];
    }

    /**
     * Write a throwaway file to the public disk and check it shows up under
     * public/storage. This is the only check that proves the whole chain, so
     * it is the one the page leads with.
     */
    public function probe(): bool
    {
        $name = '__link-probe-'.uniqid().'.txt';
        $disk = Storage::disk('public');

        try {
            if (! $disk->put($name, 'probe')) {
                return false;
            }

            return is_file($this->linkPath().DIRECTORY_SEPARATOR.$name);
        } catch (\Throwable) {
            return false;
        } finally {
            try {
                $disk->delete($name);
            } catch (\Throwable) {
                // Nothing to do — a stray 5-byte probe file is harmless.
            }
        }
    }

    /**
     * Point public/storage at storage/app/public.
     *
     * Anything already sitting at the link path is moved aside rather than
     * deleted, so a directory full of files is always recoverable.
     *
     * @return array{ok: bool, message: string, moved_aside: ?string}
     */
    public function repair(): array
    {
        $link = $this->linkPath();
        $target = $this->targetPath();
        $movedAside = null;

        if (! is_dir($target) && ! @mkdir($target, 0755, true)) {
            return $this->result(false, "The storage directory ({$target}) is missing and could not be created.");
        }

        // Checked before anything is moved: without symlink() we would strand
        // the site with no public/storage at all.
        if (! $this->symlinkSupported()) {
            return $this->result(false, 'PHP cannot create symlinks on this host — symlink() is disabled. The hosting provider has to create the link, or /storage has to be served another way.');
        }

        if (is_link($link)) {
            $resolved = realpath($link);

            if ($resolved !== false && $resolved === realpath($target)) {
                return $this->result(true, 'The link was already correct — nothing to repair.');
            }

            if (! @unlink($link)) {
                return $this->result(false, 'A symlink pointing somewhere else is in place and could not be removed.');
            }
        } elseif (file_exists($link)) {
            $movedAside = dirname($link).DIRECTORY_SEPARATOR.'storage-stale-'.date('Ymd-His');

            if (! @rename($link, $movedAside)) {
                return $this->result(false, 'public/storage is a real directory and could not be moved aside. The web user needs write permission on the public/ folder.');
            }
        }

        if (! @symlink($target, $link)) {
            return $this->result(false, 'Creating the symlink failed. The web user may not have write permission on the public/ folder.', $movedAside);
        }

        if (! $this->probe()) {
            return $this->result(false, 'The symlink was created but files still are not readable through it — check permissions on the storage folder.', $movedAside);
        }

        return $this->result(true, 'public/storage now points at storage/app/public and uploaded files are readable through it.', $movedAside);
    }

    public function symlinkSupported(): bool
    {
        if (! function_exists('symlink')) {
            return false;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        return ! in_array('symlink', $disabled, true);
    }

    private function phpUser(): ?string
    {
        if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
            return posix_getpwuid(posix_geteuid())['name'] ?? null;
        }

        return get_current_user() ?: null;
    }

    private function result(bool $ok, string $message, ?string $movedAside = null): array
    {
        return ['ok' => $ok, 'message' => $message, 'moved_aside' => $movedAside];
    }
}
