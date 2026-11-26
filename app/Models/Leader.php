<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Model;

class Leader extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable;

    protected array $compressibleImages = ['photo'];

    protected $fillable = [
        'name', 'company', 'description', 'photo', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->photo);
    }

    /**
     * Up to two initials, used on the card when no portrait is uploaded.
     */
    public function getInitialsAttribute(): string
    {
        return collect(preg_split('/\s+/', trim((string) $this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
