<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A downloadable master catalogue PDF, one per storefront download button:
 * "retail" backs the supermarket / retail buttons, "b2b" the corporate ones.
 *
 * More than one row per type is allowed; the storefront always uses the first
 * active row (by sort order) that actually has a file, so a replacement can be
 * staged and switched live by toggling Active.
 */
class Catalogue extends Model
{
    use HasMediaUrl, Publishable;

    const TYPE_RETAIL = 'retail';

    const TYPE_B2B = 'b2b';

    public const TYPES = [
        self::TYPE_RETAIL => 'Retail / Supermarket Catalogue',
        self::TYPE_B2B => 'B2B / Corporate Catalogue',
    ];

    protected $fillable = [
        'type', 'title', 'description', 'file', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * The catalogue the storefront should serve for a download button,
     * or null when nothing has been uploaded yet.
     */
    public static function forType(string $type): ?self
    {
        return static::active()
            ->ordered()
            ->where('type', $type)
            ->whereNotNull('file')
            ->first();
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->file);
    }

    /** Filename the browser saves the download as. */
    public function getDownloadNameAttribute(): string
    {
        $extension = pathinfo((string) $this->file, PATHINFO_EXTENSION) ?: 'pdf';

        return Str::slug($this->title ?: 'catalogue').'.'.$extension;
    }

    /** "2.4 MB" for the admin table; null when the file is missing on disk. */
    public function getFileSizeLabelAttribute(): ?string
    {
        $bytes = $this->fileSize();

        if ($bytes === null) {
            return null;
        }

        return $bytes >= 1048576
            ? round($bytes / 1048576, 1).' MB'
            : max(1, (int) round($bytes / 1024)).' KB';
    }

    public function fileSize(): ?int
    {
        // Seeded/static paths ("/assets/…") are not on the public disk.
        if (blank($this->file) || str_starts_with($this->file, '/') || str_starts_with($this->file, 'http')) {
            return null;
        }

        $disk = Storage::disk('public');

        return $disk->exists($this->file) ? $disk->size($this->file) : null;
    }
}
