<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryBanner extends Model
{
    use CompressesUploadedImages, HasMediaUrl;

    public const TYPE_STORE = 'store';

    public const TYPE_B2B = 'b2b';

    protected array $compressibleImages = ['background_image'];

    protected $fillable = [
        'type',
        'badge_text',
        'badge_icon',
        'title',
        'highlight',
        'subtitle',
        'cta_text',
        'cta_url',
        'background_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->background_image);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_STORE => 'Store Categories Banner',
            self::TYPE_B2B => 'B2B Categories Banner',
            default => ucfirst((string) $this->type),
        };
    }
}
