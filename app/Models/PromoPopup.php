<?php

namespace App\Models;

use App\Support\HasMediaUrl;
use App\Support\Publishable;
use App\Support\Schedulable;
use App\Traits\CompressesUploadedImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A timed interstitial — the "offer of the week" card that opens over the
 * storefront. Same publish/expire window as a promotion, plus the two things
 * only a popup needs: which pages it interrupts, and how often a visitor has
 * to see it.
 */
class PromoPopup extends Model
{
    use CompressesUploadedImages, HasMediaUrl, Publishable, Schedulable;

    protected array $compressibleImages = ['image'];

    /** The storefront pages a popup can be limited to, keyed by Inertia component name. */
    public const PAGES = [
        'Home' => 'Home',
        'Products' => 'Products',
        'Categories' => 'Supermarket Categories',
        'B2bCategories' => 'B2B Categories',
        'Promotions' => 'Offers',
        'Stores' => 'Our Stores',
        'About' => 'About',
        'Contact' => 'Contact',
    ];

    /**
     * How often a visitor sees it again. The browser enforces these — see
     * PromoPopup.vue — which is why the keys are stable strings.
     */
    public const FREQUENCIES = [
        'always' => 'Every page view',
        'once_per_visit' => 'Once per visit (until they navigate away from the site)',
        'once_per_session' => 'Once per browser session (until the tab is closed)',
        'once_per_day' => 'Once a day',
        'once_ever' => 'Only once, ever',
    ];

    protected $fillable = [
        'title', 'eyebrow', 'headline', 'body', 'image',
        'cta_text', 'cta_url', 'cta_opens_enquiry',
        'audience', 'pages', 'frequency', 'delay_seconds',
        'sort_order', 'is_active', 'publish_at', 'expire_at',
    ];

    protected $casts = [
        'pages' => 'array',
        'cta_opens_enquiry' => 'boolean',
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->scheduleWindow();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }

    /**
     * The one popup to show on a given Inertia page, or null. Ordered rows mean
     * the admin decides which wins when two overlap — only ever one at a time,
     * because stacking interstitials is how you lose a visitor.
     */
    public static function forPage(?string $component): ?self
    {
        if ($component === null) {
            return null;
        }

        return static::active()->ordered()->get()
            ->first(fn (self $popup) => $popup->appearsOn($component));
    }

    public function appearsOn(string $component): bool
    {
        if ($this->audience !== 'selected') {
            return true;
        }

        return in_array($component, $this->pages ?? [], true);
    }

    /** The shape PromoPopup.vue expects. */
    public function toStorefrontArray(): array
    {
        return [
            'id' => $this->id,
            'eyebrow' => $this->eyebrow,
            'headline' => $this->headline ?: $this->title,
            'body' => $this->body,
            'image' => $this->image_url,
            'ctaText' => $this->cta_text,
            'ctaUrl' => $this->cta_url,
            'ctaOpensEnquiry' => (bool) $this->cta_opens_enquiry,
            'frequency' => $this->frequency ?: 'once_per_session',
            'delaySeconds' => (int) $this->delay_seconds,
            // The browser re-checks the window on a ticker, so a popup that
            // expires mid-visit stops opening without a reload.
            'publishesAt' => $this->scheduleIso($this->publish_at),
            'expiresAt' => $this->scheduleIso($this->expire_at),
        ];
    }
}
