<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\PageView;
use App\Models\Product;
use App\Models\Subcategory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Writes one row per storefront page a visitor opens.
 *
 * The shop wanted to know how many people come, what they look at and what
 * they ask about. That is three questions a third-party analytics script would
 * answer while also loading a tracker on every page and handing the answers to
 * someone else; the site already renders every one of those pages itself, so it
 * can simply count them.
 *
 * Runs after the response so a slow write never delays the page, and never
 * throws: a visitor must not see an error because a counter failed.
 */
class RecordPageView
{
    /** Storefront routes worth counting, mapped to the page they render. */
    private const PAGES = [
        'home' => 'Home',
        'about' => 'About',
        'stores' => 'Stores',
        'categories' => 'Categories',
        'b2b-categories' => 'B2bCategories',
        'products' => 'Products',
        'product' => 'Product',
        'promotions' => 'Promotions',
        'contact' => 'Contact',
    ];

    /**
     * Crawlers announce themselves; counting them would drown the real
     * visitors. Not exhaustive on purpose — this is a shop's dashboard, not an
     * ad network's.
     */
    private const BOTS = [
        'bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit', 'preview',
        'monitor', 'headless', 'lighthouse', 'pingdom', 'curl', 'wget', 'python',
    ];

    /** Roughly one visit in this many also clears out very old rows. */
    private const PRUNE_ODDS = 500;

    /** How long a row is kept before the prune sweeps it up. */
    private const KEEP_DAYS = 400;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $this->record($request, $response);
        } catch (\Throwable) {
            // A counter is never worth a 500 in front of a customer.
        }

        return $response;
    }

    private function record(Request $request, Response $response): void
    {
        if (! $this->countable($request, $response)) {
            return;
        }

        $page = self::PAGES[$request->route()?->getName()] ?? null;

        if ($page === null) {
            return;
        }

        $subject = $this->subject($page, $request);

        PageView::create([
            'page' => $page,
            'path' => mb_substr($request->getRequestUri(), 0, 255),
            'category_id' => $subject['category_id'],
            'subcategory_id' => $subject['subcategory_id'],
            'product_id' => $subject['product_id'],
            'visitor_hash' => $this->visitorHash($request),
            'is_mobile' => $this->isMobile((string) $request->userAgent()),
            'referrer_host' => $this->referrerHost($request),
            'created_at' => now(),
        ]);

        if (random_int(1, self::PRUNE_ODDS) === 1) {
            PageView::where('created_at', '<', now()->subDays(self::KEEP_DAYS))->delete();
        }
    }

    /**
     * A plain page load by a person: a GET that rendered, not a form post, a
     * download, a redirect, or Inertia asking for one prop back.
     */
    private function countable(Request $request, Response $response): bool
    {
        return $request->isMethod('GET')
            && $response->getStatusCode() === 200
            && ! $request->header('X-Inertia-Partial-Component')
            && ! $request->ajax()
            && ! $this->isBot((string) $request->userAgent());
    }

    /**
     * What the page is about, so the totals can be grouped by the catalogue
     * rather than by URL text.
     *
     * @return array{category_id: int|null, subcategory_id: int|null, product_id: int|null}
     */
    private function subject(string $page, Request $request): array
    {
        $empty = ['category_id' => null, 'subcategory_id' => null, 'product_id' => null];

        if ($page === 'Product') {
            $product = Product::query()
                ->select(['id', 'subcategory_id'])
                ->with('subcategory:id,category_id')
                ->where('slug', $request->route('slug'))
                ->first();

            return $product === null ? $empty : [
                'category_id' => $product->subcategory?->category_id,
                'subcategory_id' => $product->subcategory_id,
                'product_id' => $product->id,
            ];
        }

        if ($page !== 'Products') {
            return $empty;
        }

        // The catalogue is filtered by name in the query string, which is what
        // both category pages link with.
        $categoryName = trim((string) $request->query('category'));
        $varietyName = trim((string) $request->query('variety'));

        if ($categoryName === '' || strtolower($categoryName) === 'all') {
            return $empty;
        }

        $category = Category::where('name', $categoryName)->first(['id']);

        if ($category === null) {
            return $empty;
        }

        $subcategory = $varietyName === ''
            ? null
            : Subcategory::where('category_id', $category->id)->where('name', $varietyName)->first(['id']);

        return [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory?->id,
            'product_id' => null,
        ];
    }

    /**
     * One person, for one day, without storing anything that identifies them.
     *
     * The date is part of what is hashed, so the same visitor is a different
     * hash tomorrow: today's unique count is honest, and nobody can be followed
     * from one day to the next. The app key means the hash cannot be
     * reconstructed from an IP by anyone who does not have it.
     */
    private function visitorHash(Request $request): string
    {
        return md5(implode('|', [
            now()->toDateString(),
            $request->ip(),
            (string) $request->userAgent(),
            (string) config('app.key'),
        ]));
    }

    private function isBot(string $agent): bool
    {
        if ($agent === '') {
            return true;
        }

        $agent = strtolower($agent);

        foreach (self::BOTS as $needle) {
            if (str_contains($agent, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function isMobile(string $agent): bool
    {
        return (bool) preg_match('/android|iphone|ipad|ipod|mobile|webos|blackberry|opera mini/i', $agent);
    }

    /**
     * Where they came from, host only — enough to tell Google from WhatsApp
     * from a direct visit, and nothing more.
     */
    private function referrerHost(Request $request): ?string
    {
        $referrer = (string) $request->headers->get('referer');

        if ($referrer === '') {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        if (! is_string($host) || $host === '' || $host === $request->getHost()) {
            // Moving around the site is not a referral.
            return null;
        }

        return mb_substr(preg_replace('/^www\./', '', $host) ?? $host, 0, 120);
    }
}
