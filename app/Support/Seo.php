<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * The head every crawler reads.
 *
 * This site has no server-side rendering: the Vue pages are built in the
 * browser, so a title or an og: tag set from a page component arrives long
 * after WhatsApp, Facebook or a plain crawler has already read the document.
 * Everything here is therefore assembled in PHP and printed by
 * app.blade.php — from the same `meta` prop every controller already passes,
 * plus the site settings the admin can edit.
 */
class Seo
{
    public const SITE_NAME = 'Jay Tulsi Agency & Vinayak Stores';

    public const DEFAULT_TITLE = 'Jay Tulsi Agency & Vinayak Stores | Retail & B2B Supply, Surat';

    public const DEFAULT_DESCRIPTION = 'Supermarket chain and B2B commercial supply partner in Surat since 1984 — groceries, FMCG, dairy, housekeeping and industrial supplies for homes, businesses and institutions.';

    /** Fallback share image, used until one is set in Site Settings. */
    public const DEFAULT_IMAGE = 'assets/logo.png';

    /**
     * Query keys that name a page rather than a visitor's own narrowing.
     *
     * /products?category=Grocery is a page worth indexing on its own;
     * /products?search=atta&sort=low-high is the same page seen through one
     * visitor's filters, and every combination of those would otherwise look
     * like a separate URL with duplicate content.
     */
    private const CANONICAL_QUERY = ['segment', 'category', 'variety'];

    /**
     * Everything app.blade.php prints, for one request.
     *
     * @param  array<string, mixed>  $page  the Inertia page array
     * @return array<string, mixed>
     */
    public static function forPage(array $page, Request $request): array
    {
        $props = $page['props'] ?? [];
        $meta = is_array($props['meta'] ?? null) ? $props['meta'] : [];

        $title = trim((string) ($meta['title'] ?? '')) ?: self::setting('seo_default_title', self::DEFAULT_TITLE);
        $description = trim((string) ($meta['description'] ?? '')) ?: self::setting('seo_default_description', self::DEFAULT_DESCRIPTION);

        return [
            'title' => $title,
            'description' => self::trim($description, 300),
            'canonical' => self::canonical($request),
            // A page may name its own picture — a product page hands over the
            // product's photo, which is what a shared link should show rather
            // than the shop's logo. Everything else falls back to the site
            // image set in Site Settings.
            'image' => self::imageUrl(
                trim((string) ($meta['image'] ?? '')) ?: (self::setting('seo_og_image', '') ?: self::DEFAULT_IMAGE)
            ),
            'robots' => self::robots($request),
            'siteName' => self::SITE_NAME,
            'verification' => self::setting('seo_google_verification', ''),
            'jsonLd' => self::jsonLd($page, $request),
        ];
    }

    /**
     * The one URL this page should be indexed under.
     *
     * Built rather than taken from the request so that the filters, the search
     * box and the sort order all fold back onto the page they belong to.
     */
    private static function canonical(Request $request): string
    {
        $kept = [];

        foreach (self::CANONICAL_QUERY as $key) {
            $value = $request->query($key);

            if (is_string($value) && trim($value) !== '' && strtolower($value) !== 'all') {
                $kept[$key] = $value;
            }
        }

        $url = self::url($request->getPathInfo());

        return $kept === [] ? $url : $url.'?'.http_build_query($kept);
    }

    /**
     * A results page is not a page — it is one visitor's question, and a
     * thousand of them in the index only compete with the real pages. Followed
     * either way, so the products it lists still get crawled.
     */
    private static function robots(Request $request): string
    {
        return filled($request->query('search')) ? 'noindex, follow' : 'index, follow';
    }

    /**
     * An absolute URL for a path, with the home page written the one way.
     *
     * `url('/')` drops the trailing slash, which would leave the sitemap
     * pointing at a URL the canonical tag spells differently.
     */
    public static function url(string $path): string
    {
        return $path === '/' ? rtrim(url('/'), '/').'/' : url($path);
    }

    /**
     * Same three rules as {@see HasMediaUrl}: an absolute URL and a shipped
     * `/assets/...` path are used as they are, anything else is an upload.
     */
    public static function imageUrl(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return asset(self::DEFAULT_IMAGE);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return url($path);
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Structured data: who the business is, and — on the stores page — where
     * each of its outlets is.
     *
     * @param  array<string, mixed>  $page
     * @return array<int, array<string, mixed>>
     */
    private static function jsonLd(array $page, Request $request): array
    {
        $graph = [self::organization(), self::website()];

        $component = $page['component'] ?? '';
        $stores = $page['props']['stores'] ?? null;

        if ($component === 'Stores' && is_array($stores) && $stores !== []) {
            $graph[] = self::storeList($stores);
        }

        if ($component === 'Products' && filled($request->query('category'))) {
            $graph[] = self::breadcrumbs($request);
        }

        return $graph;
    }

    /**
     * @return array<string, mixed>
     */
    private static function organization(): array
    {
        $settings = Setting::map();

        $phones = array_values(array_filter([
            self::phone($settings['phone_primary'] ?? ''),
            self::phone($settings['phone_secondary'] ?? ''),
        ]));

        $socials = array_values(array_filter([
            $settings['social_facebook'] ?? '',
            $settings['social_instagram'] ?? '',
            $settings['social_linkedin'] ?? '',
        ], fn ($url) => filled($url) && $url !== '#'));

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => $settings['business_legal_name'] ?? self::SITE_NAME,
            'alternateName' => self::SITE_NAME,
            'url' => url('/'),
            'logo' => asset(self::DEFAULT_IMAGE),
            'image' => asset(self::DEFAULT_IMAGE),
            'description' => self::setting('seo_default_description', self::DEFAULT_DESCRIPTION),
            'foundingDate' => $settings['business_established'] ?? null,
            'email' => $settings['email_primary'] ?? null,
            'telephone' => $phones[0] ?? null,
            'areaServed' => $settings['business_service_area'] ?? null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings['address_full'] ?? null,
                'addressLocality' => 'Surat',
                'addressRegion' => 'Gujarat',
                'addressCountry' => 'IN',
            ],
        ];

        if (count($phones) > 1) {
            $data['contactPoint'] = array_map(fn (string $number) => [
                '@type' => 'ContactPoint',
                'telephone' => $number,
                'contactType' => 'customer service',
                'areaServed' => 'IN',
            ], $phones);
        }

        if ($socials !== []) {
            $data['sameAs'] = $socials;
        }

        return array_filter($data, fn ($value) => $value !== null && $value !== '');
    }

    /**
     * Tells a search engine the site has its own catalogue search, which is
     * what a "search this site" box in the results is built from.
     *
     * @return array<string, mixed>
     */
    private static function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => self::SITE_NAME,
            'publisher' => ['@id' => url('/').'#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/products').'?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Each outlet as its own business, which is what puts a chain's branches
     * on the map rather than only its head office.
     *
     * @param  array<int, array<string, mixed>>  $stores
     * @return array<string, mixed>
     */
    private static function storeList(array $stores): array
    {
        $items = [];

        foreach (array_values($stores) as $index => $store) {
            if (! is_array($store) || blank($store['name'] ?? null)) {
                continue;
            }

            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => array_filter([
                    '@type' => 'GroceryStore',
                    'name' => $store['name'],
                    // The props carry a site-relative path; structured data
                    // only counts an absolute one.
                    'image' => filled($store['image'] ?? null) ? self::imageUrl($store['image']) : null,
                    'telephone' => self::phone((string) ($store['phone'] ?? '')),
                    'email' => $store['email'] ?? null,
                    'hasMap' => $store['mapUrl'] ?? null,
                    'parentOrganization' => ['@id' => url('/').'#organization'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $store['address'] ?? null,
                        'addressLocality' => 'Surat',
                        'addressRegion' => 'Gujarat',
                        'addressCountry' => 'IN',
                    ],
                ], fn ($value) => $value !== null && $value !== ''),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Our stores and outlets',
            'itemListElement' => $items,
        ];
    }

    /**
     * The trail the catalogue page already draws on screen, in the form Google
     * prints under a result instead of the bare URL.
     *
     * @return array<string, mixed>
     */
    private static function breadcrumbs(Request $request): array
    {
        $segment = strtolower((string) $request->query('segment'));
        $isB2b = str_contains($segment, 'b2b');
        $category = trim((string) $request->query('category'));
        $variety = trim((string) $request->query('variety'));

        $trail = [
            ['name' => 'Home', 'url' => self::url('/')],
            [
                'name' => $isB2b ? 'B2B Categories' : 'Store Categories',
                'url' => self::url($isB2b ? '/b2b-categories' : '/categories'),
            ],
        ];

        $categoryUrl = self::url('/products').'?'.http_build_query(array_filter([
            'segment' => $isB2b ? 'b2b' : 'retail',
            'category' => $category,
        ]));

        $trail[] = ['name' => $category, 'url' => $categoryUrl];

        if (filled($variety)) {
            $trail[] = ['name' => $variety, 'url' => $categoryUrl.'&variety='.rawurlencode($variety)];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn (array $crumb, int $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ], $trail, array_keys($trail)),
        ];
    }

    /**
     * One dialable number in international form.
     *
     * Settings hold bare ten-digit numbers and a store row often lists two in
     * the same cell ("9099081400 / 9099081409"); structured data wants a single
     * number, so the first one is taken and given its country code.
     */
    private static function phone(string $number): ?string
    {
        $cleaned = preg_replace('/[\s\-()]+/', '', trim($number)) ?? '';
        $first = preg_split('#[/,;|&]+#', $cleaned, -1, PREG_SPLIT_NO_EMPTY)[0] ?? '';
        $digits = preg_replace('/[^0-9+]/', '', $first) ?? '';

        if ($digits === '' || $digits === '+') {
            return null;
        }

        if (str_starts_with($digits, '+')) {
            return $digits;
        }

        return strlen($digits) === 10 ? '+91'.$digits : '+'.$digits;
    }

    private static function setting(string $key, string $default): string
    {
        $value = trim((string) Setting::val($key, ''));

        return $value !== '' ? $value : $default;
    }

    /** Search results cut a description off anyway; cut it on a word. */
    private static function trim(string $text, int $limit): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?? '');

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, mb_strrpos(mb_substr($text, 0, $limit), ' ') ?: $limit), ' ,.–-').'…';
    }
}
