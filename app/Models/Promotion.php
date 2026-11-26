<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Support\Schedulable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable, Schedulable;

    /** The offers-page groups, keyed by the value stored in promotions.group. */
    public const GROUPS = [
        'latest' => 'Latest Offers',
        'festive' => 'Festive Offers',
        'seasonal' => 'Seasonal Sale',
        'bulk' => 'Bulk Offers',
    ];

    protected array $compressibleImages = ['image'];

    protected $fillable = [
        'title', 'badge', 'badge_bg', 'discount', 'discount_color', 'subtitle',
        'description', 'validity', 'image', 'card_bg', 'border_color', 'is_dark',
        'group', 'cta_text', 'cta_url', 'show_on_home', 'show_on_page', 'sort_order', 'is_active',
        'publish_at', 'expire_at',
    ];

    protected $casts = [
        'is_dark' => 'boolean',
        'show_on_home' => 'boolean',
        'show_on_page' => 'boolean',
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->scheduleWindow();
    }

    public function scopeOnHome(Builder $query): Builder
    {
        return $query->where('show_on_home', true);
    }

    public function scopeOnPage(Builder $query): Builder
    {
        return $query->where('show_on_page', true);
    }

    public function getDiscountColorAttribute(?string $value): ?string
    {
        return $value ?: $this->badge_bg;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }
}
