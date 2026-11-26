<?php

namespace App\Http\Middleware;

use App\Models\Catalogue;
use App\Models\Category;
use App\Models\PromoPopup;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Site-wide content every page needs. Sharing it here keeps the chrome
     * components (top bar, footer, contact band, WhatsApp button) dynamic
     * without every controller having to pass the same props.
     */
    public function share(Request $request): array
    {
        $settings = Setting::map();

        $primary = $settings['phone_primary'] ?? '';
        $secondary = $settings['phone_secondary'] ?? '';

        return array_merge(parent::share($request), [
            'site' => [
                // Stamped with the file's own mtime. The logo was replaced with
                // a transparent version, but the URL never changed, so browsers
                // went on showing the old one — a white square behind the
                // emblem, which is plain to see against the navy footer.
                'logoUrl' => $this->logoUrl(),
                'phonePrimary' => $primary,
                'phoneSecondary' => $secondary,
                'phoneDisplay' => Setting::phoneDisplay(),
                'emailPrimary' => $settings['email_primary'] ?? '',
                'emailSecondary' => $settings['email_secondary'] ?? '',
                'addressShort' => $settings['address_short'] ?? '',
                'addressFooter' => $settings['address_footer'] ?? '',
                'addressFull' => $settings['address_full'] ?? '',
                'storeHours' => $settings['store_hours'] ?? '',
                'footerTagline' => $settings['footer_tagline'] ?? '',
                // Null where the admin has not filled one in, so the icon is
                // left off rather than sitting there doing nothing when it is
                // clicked.
                'social' => [
                    'facebook' => $this->socialUrl($settings['social_facebook'] ?? '', 'https://www.facebook.com/'),
                    'instagram' => $this->socialUrl($settings['social_instagram'] ?? '', 'https://www.instagram.com/'),
                    'linkedin' => $this->socialUrl($settings['social_linkedin'] ?? '', 'https://www.linkedin.com/company/'),
                ],
            ],
            // The footer's two category columns. Read from the catalogue so a
            // renamed or retired category is not left as a dead link at the
            // bottom of every page.
            'footerCategories' => $this->footerCategories(),
            'whatsapp' => [
                'enabled' => Setting::bool('whatsapp_enabled'),
                'number' => $settings['whatsapp_number'] ?? '',
                'message' => $settings['whatsapp_message'] ?? '',
                'url' => $this->whatsappUrl($settings),
            ],
            'catalogues' => $this->catalogues(),
            // The clock the storefront retires scheduled content against.
            // Visitor devices can be minutes or days off, so useLiveSchedule
            // measures the browser against this rather than trusting it.
            'serverTime' => now()->toIso8601String(),
            // Shared rather than per-controller: a popup can be scoped to any
            // page, and PromoPopup::forPage() already decides whether this one
            // qualifies. Null on every page it does not apply to.
            'promoPopup' => $this->promoPopup($request),
        ]);
    }

    /**
     * A social link the browser can actually follow, or null so the icon is
     * left off the page entirely.
     *
     * The admin might paste a whole profile URL or type just the handle; "#"
     * is what the seeder leaves behind for a page that does not exist yet.
     */
    private function socialUrl(string $value, string $base): ?string
    {
        $value = trim($value);

        if ($value === '' || $value === '#') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, 'www.')) {
            return 'https://'.$value;
        }

        // A bare handle, with or without the @ people habitually type first.
        return $base.ltrim($value, '@/');
    }

    /**
     * The category links in the footer, one column per side of the business.
     *
     * Capped rather than listing everything: the footer is a shortcut to the
     * busiest aisles, and both category pages already show the full set.
     *
     * @return array<string, array<int, array{name: string, icon: string, href: string}>>
     */
    private function footerCategories(): array
    {
        $categories = Category::active()->ordered()->get(['id', 'name', 'type', 'icon']);

        $columns = [];

        foreach ([Category::TYPE_STORE => 'retail', Category::TYPE_B2B => 'b2b'] as $type => $segment) {
            $columns[$type] = $categories
                ->where('type', $type)
                ->take(6)
                ->map(fn (Category $category) => [
                    'name' => $category->name,
                    'icon' => $category->icon ?: 'fa-tag',
                    'href' => '/products?segment='.$segment.'&category='.rawurlencode($category->name),
                ])
                ->values()
                ->all();
        }

        return $columns;
    }

    /**
     * The logo with a version on the end, so a replaced file is actually
     * fetched rather than served from a year-old cache. Same trick the admin
     * panel uses for its stylesheet.
     */
    private function logoUrl(): string
    {
        $path = public_path('assets/logo.png');
        $version = is_file($path) ? filemtime($path) : null;

        return asset('assets/logo.png').($version ? '?v='.$version : '');
    }

    /**
     * Route name -> Inertia page, so a popup scoped to "Offers" in the admin
     * matches /promotions. share() runs before Inertia has resolved the
     * component, which is why this maps the route rather than reading it.
     */
    private const ROUTE_PAGES = [
        'home' => 'Home',
        'about' => 'About',
        'stores' => 'Stores',
        'categories' => 'Categories',
        'b2b-categories' => 'B2bCategories',
        'products' => 'Products',
        'promotions' => 'Promotions',
        'contact' => 'Contact',
    ];

    /**
     * The single popup due on this page, if any. Only GET page loads get one —
     * a form post should never come back with an interstitial.
     */
    private function promoPopup(Request $request): ?array
    {
        if (! $request->isMethod('GET')) {
            return null;
        }

        $page = self::ROUTE_PAGES[$request->route()?->getName()] ?? null;

        return PromoPopup::forPage($page)?->toStorefrontArray();
    }

    /**
     * The master catalogue behind each download button, keyed by type. A null
     * entry means nothing is uploaded yet — the storefront then opens the
     * "coming soon" modal instead of downloading.
     */
    private function catalogues(): array
    {
        $rows = Catalogue::active()
            ->ordered()
            ->whereNotNull('file')
            ->get()
            ->groupBy('type');

        $props = [];

        foreach (array_keys(Catalogue::TYPES) as $type) {
            // First active row of that type wins, matching Catalogue::forType().
            $catalogue = $rows->get($type)?->first();

            $props[$type] = $catalogue === null ? null : [
                'title' => $catalogue->title,
                'description' => $catalogue->description,
                'url' => $catalogue->file_url,
                'downloadUrl' => route('catalogue.download', $type),
                'size' => $catalogue->file_size_label,
            ];
        }

        return $props;
    }

    private function whatsappUrl(array $settings): ?string
    {
        $number = preg_replace('/\D/', '', $settings['whatsapp_number'] ?? '');

        if ($number === '') {
            return null;
        }

        $message = $settings['whatsapp_message'] ?? '';

        return 'https://wa.me/'.$number.($message ? '?text='.rawurlencode($message) : '');
    }
}
