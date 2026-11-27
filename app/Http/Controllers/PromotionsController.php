<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\Promotion;
use App\Models\Reel;
use Inertia\Inertia;
use Inertia\Response;

class PromotionsController extends Controller
{
    /**
     * The four groups on the page, in display order. The keys match
     * promotions.group; the ids match the anchors the sticky tab bar scrolls to.
     */
    private const GROUPS = [
        ['key' => 'latest', 'id' => 'latest-offers', 'badge' => 'Fresh Deals', 'title' => 'Latest Offers', 'icon' => 'fa-certificate'],
        ['key' => 'festive', 'id' => 'festive-offers', 'badge' => 'Celebrations', 'title' => 'Festive Offers', 'icon' => 'fa-gift'],
        ['key' => 'seasonal', 'id' => 'seasonal-sale', 'badge' => 'Special Savings', 'title' => 'Seasonal Sale', 'icon' => 'fa-tag'],
        ['key' => 'bulk', 'id' => 'bulk-offers', 'badge' => 'B2B Wholesale', 'title' => 'Bulk Offers', 'icon' => 'fa-boxes-packing'],
    ];

    /**
     * Cards the server sent as live can still expire while the tab sits open,
     * so every promotion also carries its raw window as ISO-8601. The page
     * re-checks it on a ticker and drops the card without a reload —
     * `expire_at` here stays the human-readable string the card prints.
     */
    public static function schedule(Promotion $promotion): array
    {
        return [
            'publish_at' => $promotion->publish_at?->format('j M Y'),
            'expire_at' => $promotion->expire_at?->format('j M Y'),
            'publishesAt' => $promotion->scheduleIso($promotion->publish_at),
            'expiresAt' => $promotion->scheduleIso($promotion->expire_at),
        ];
    }

    public function index(): Response
    {
        $promotions = Promotion::active()->onPage()->ordered()->get()->groupBy('group');

        $groups = collect(self::GROUPS)->map(fn (array $group) => $group + [
            'promotions' => $promotions->get($group['key'], collect())
                ->map(fn (Promotion $promotion) => self::schedule($promotion) + [
                    'id' => $promotion->id,
                    'badge' => $promotion->badge,
                    'badgeBg' => $promotion->badge_bg,
                    'title' => $promotion->title,
                    'discount' => $promotion->discount,
                    'discountColor' => $promotion->discount_color,
                    'subtitle' => $promotion->subtitle,
                    'validity' => $promotion->validity,
                    'image' => $promotion->image_url,
                    'cardBg' => $promotion->card_bg,
                    'borderColor' => $promotion->border_color,
                    'isDark' => $promotion->is_dark,
                    'ctaText' => $promotion->cta_text,
                    'ctaUrl' => $promotion->cta_url,
                ])->values(),
        ])->values();

        return Inertia::render('Promotions', [
            'meta' => [
                'title' => 'Offers & Promotions | VIZORA FOODS',
                'description' => 'Explore latest discount offers, supermarket ration combos, seasonal sales, and bulk buy B2B deals at Jay Tulsi Agency.',
            ],
            'groups' => $groups,
            'reels' => Reel::active()->ordered()->get()
                ->map(fn (Reel $reel) => [
                    'id' => $reel->id,
                    'title' => $reel->title,
                    'caption' => $reel->caption,
                    'cover' => $reel->cover_url,
                    'coverFallback' => $reel->cover_fallback_url,
                    'embedUrl' => $reel->embed_url,
                    'previewUrl' => $reel->preview_url,
                    'permalink' => $reel->permalink,
                ])->values(),
            // active() — not a bare is_active check — so a gallery image's
            // publish/expire window is honoured here as it is on the home page.
            'gallery' => GalleryImage::active()->photos()->ordered()->get()
                ->map(fn (GalleryImage $img) => $img->toCardArray())->values(),
            // Videos are the same resource, kept apart so the page can head
            // them separately: photos are a grid, videos play in place.
            'galleryVideos' => GalleryImage::active()->videos()->ordered()->get()
                ->map(fn (GalleryImage $video) => $video->toCardArray())
                ->filter(fn (array $video) => filled($video['embedUrl']))
                ->values(),
        ]);
    }
}
