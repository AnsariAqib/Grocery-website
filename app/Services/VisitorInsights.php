<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\PageView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Every number the panel's Visitor Insights page prints.
 *
 * Kept out of the page so the queries read as one thing, and so the same
 * figures could be reused — an emailed weekly summary, say — without lifting
 * them out of a Livewire component.
 *
 * "Visitors" always means distinct visitor hashes, which is a person a day
 * rather than a person forever: see RecordPageView::visitorHash().
 */
class VisitorInsights
{
    /** How many rows a "top" list shows. */
    private const TOP = 10;

    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
    ) {}

    public static function forDays(int $days): self
    {
        return new self(
            now()->subDays(max(1, $days) - 1)->startOfDay(),
            now()->endOfDay(),
        );
    }

    /**
     * The headline figures, each with the same figure for the previous stretch
     * of equal length so the page can say whether things are going up.
     *
     * @return array<string, array{value: int|string, previous: int|string}>
     */
    public function totals(): array
    {
        $length = $this->from->diffInDays($this->to) + 1;
        $previous = new self(
            $this->from->copy()->subDays($length),
            $this->from->copy()->subSecond(),
        );

        $views = $this->views()->count();
        $visitors = $this->uniqueVisitors();

        $wasViews = $previous->views()->count();
        $wasVisitors = $previous->uniqueVisitors();

        return [
            'visitors' => ['value' => $visitors, 'previous' => $wasVisitors],
            'views' => ['value' => $views, 'previous' => $wasViews],
            'productViews' => [
                'value' => $this->views()->whereNotNull('product_id')->count(),
                'previous' => $previous->views()->whereNotNull('product_id')->count(),
            ],
            'inquiries' => ['value' => $this->inquiries()->count(), 'previous' => $previous->inquiries()->count()],
            'pagesPerVisit' => [
                'value' => $visitors > 0 ? round($views / $visitors, 1) : 0,
                'previous' => $wasVisitors > 0 ? round($wasViews / $wasVisitors, 1) : 0,
            ],
        ];
    }

    public function uniqueVisitors(): int
    {
        return $this->views()->distinct()->count('visitor_hash');
    }

    /**
     * Visitors and views for each day in the range, including the days nobody
     * came — a gap in a chart should read as zero, not as missing.
     *
     * @return array<int, array{date: string, label: string, visitors: int, views: int}>
     */
    public function daily(): array
    {
        $rows = $this->views()
            ->selectRaw('date(created_at) as day, count(*) as views, count(distinct visitor_hash) as visitors')
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $days = [];

        for ($date = $this->from->copy(); $date->lte($this->to); $date->addDay()) {
            $key = $date->toDateString();
            $row = $rows->get($key);

            $days[] = [
                'date' => $key,
                'label' => $date->format('d M'),
                'views' => (int) ($row->views ?? 0),
                'visitors' => (int) ($row->visitors ?? 0),
            ];
        }

        return $days;
    }

    /**
     * Categories by how many people looked at them — a category page view and
     * any product inside it both count, because both are someone looking at
     * that part of the shop.
     *
     * @return array<int, array{name: string, views: int, visitors: int}>
     */
    public function topCategories(): array
    {
        return $this->topBy('category_id', 'categories');
    }

    /**
     * @return array<int, array{name: string, views: int, visitors: int}>
     */
    public function topSubcategories(): array
    {
        return $this->topBy('subcategory_id', 'subcategories');
    }

    /**
     * @return array<int, array{name: string, views: int, visitors: int}>
     */
    public function topProducts(): array
    {
        return $this->topBy('product_id', 'products');
    }

    /**
     * What people actually asked about, from the enquiry forms rather than
     * from the page counts — the question the shop cares most about.
     *
     * @return array<int, array{name: string, count: int}>
     */
    public function topInquiryProducts(): array
    {
        return $this->inquiries()
            ->whereNotNull('product_name')
            ->where('product_name', '!=', '')
            ->selectRaw('product_name as name, count(*) as count')
            ->groupBy('product_name')
            ->orderByDesc('count')
            ->limit(self::TOP)
            ->get()
            ->map(fn ($row) => ['name' => (string) $row->name, 'count' => (int) $row->count])
            ->all();
    }

    /**
     * @return array<int, array{name: string, count: int}>
     */
    public function topInquiryCategories(): array
    {
        return $this->inquiries()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->selectRaw('category as name, count(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(self::TOP)
            ->get()
            ->map(fn ($row) => ['name' => (string) $row->name, 'count' => (int) $row->count])
            ->all();
    }

    /**
     * Retail, B2B or dealership — which side of the business is being asked.
     *
     * @return array<int, array{name: string, count: int}>
     */
    public function inquiryTypes(): array
    {
        $labels = ['retail' => 'Retail / store', 'b2b' => 'B2B / bulk supply', 'dealership' => 'Dealership'];

        return $this->inquiries()
            ->selectRaw('inquiry_type as type, count(*) as count')
            ->groupBy('inquiry_type')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'name' => $labels[$row->type] ?? ucfirst((string) $row->type),
                'count' => (int) $row->count,
            ])
            ->all();
    }

    /**
     * Where the visits came from. "Direct" covers a typed address, a bookmark
     * and anything that hid its referrer.
     *
     * @return array<int, array{name: string, visitors: int}>
     */
    public function topReferrers(): array
    {
        $rows = $this->views()
            ->selectRaw('referrer_host as host, count(distinct visitor_hash) as visitors')
            ->groupBy('referrer_host')
            ->orderByDesc('visitors')
            ->limit(self::TOP)
            ->get();

        return $rows->map(fn ($row) => [
            'name' => filled($row->host) ? (string) $row->host : 'Direct / unknown',
            'visitors' => (int) $row->visitors,
        ])->all();
    }

    /**
     * Phone against everything else, as a share of visitors.
     *
     * @return array{mobile: int, desktop: int, mobileShare: int}
     */
    public function devices(): array
    {
        $mobile = (clone $this->views())->where('is_mobile', true)->distinct()->count('visitor_hash');
        $desktop = (clone $this->views())->where('is_mobile', false)->distinct()->count('visitor_hash');
        $total = $mobile + $desktop;

        return [
            'mobile' => $mobile,
            'desktop' => $desktop,
            'mobileShare' => $total > 0 ? (int) round($mobile / $total * 100) : 0,
        ];
    }

    /**
     * Which pages were opened, by name — the plain "where did they go" list.
     *
     * @return array<int, array{name: string, views: int, visitors: int}>
     */
    public function topPages(): array
    {
        $labels = [
            'Home' => 'Home', 'Products' => 'Product catalogue', 'Product' => 'Single product pages',
            'Categories' => 'Store Categories', 'B2bCategories' => 'B2B Categories',
            'Stores' => 'Our Stores', 'Promotions' => 'Updates & Offers',
            'About' => 'About Us', 'Contact' => 'Contact Us',
        ];

        return $this->views()
            ->selectRaw('page, count(*) as views, count(distinct visitor_hash) as visitors')
            ->groupBy('page')
            ->orderByDesc('views')
            ->get()
            ->map(fn ($row) => [
                'name' => $labels[$row->page] ?? (string) $row->page,
                'views' => (int) $row->views,
                'visitors' => (int) $row->visitors,
            ])
            ->all();
    }

    public function hasData(): bool
    {
        return PageView::query()->exists();
    }

    /**
     * A "top" list over one of the catalogue columns, joined to the table that
     * holds the names so a deleted row cannot leave a bare id on screen.
     *
     * @return array<int, array{name: string, views: int, visitors: int}>
     */
    private function topBy(string $column, string $table): array
    {
        return $this->views()
            ->join($table, "{$table}.id", '=', "page_views.{$column}")
            ->selectRaw("{$table}.name as name, count(*) as views, count(distinct visitor_hash) as visitors")
            ->groupBy("{$table}.name")
            ->orderByDesc('views')
            ->limit(self::TOP)
            ->get()
            ->map(fn ($row) => [
                'name' => (string) $row->name,
                'views' => (int) $row->views,
                'visitors' => (int) $row->visitors,
            ])
            ->all();
    }

    /** @return Builder<PageView> */
    private function views()
    {
        return PageView::query()->whereBetween('page_views.created_at', [$this->from, $this->to]);
    }

    /** @return Builder<Inquiry> */
    private function inquiries()
    {
        return Inquiry::query()->whereBetween('created_at', [$this->from, $this->to]);
    }

    public function rangeLabel(): string
    {
        return $this->from->format('d M Y').' — '.$this->to->format('d M Y');
    }
}
