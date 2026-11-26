<?php

namespace App\Filament\Pages;

use App\Services\VisitorInsights;
use BackedEnum;
use Filament\Pages\Page;
use Livewire\Attributes\Url;

/**
 * How many people came, what they looked at, and what they asked for.
 *
 * The counting is the site's own — see RecordPageView — so this page needs no
 * third-party script, no account anywhere, and it can group by the actual
 * catalogue rows instead of guessing a category out of a URL.
 */
class VisitorInsightsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Business';

    protected static ?string $navigationLabel = 'Visitor Insights';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Visitor Insights';

    protected static ?string $slug = 'visitor-insights';

    protected string $view = 'filament.pages.visitor-insights';

    /** The stretch being shown, in days. Kept in the URL so a view can be shared. */
    #[Url]
    public int $days = 7;

    /** The ranges offered, and what each button says. */
    public const RANGES = [
        1 => 'Today',
        7 => 'Last 7 days',
        30 => 'Last 30 days',
        90 => 'Last 90 days',
        365 => 'Last year',
    ];

    public function mount(): void
    {
        if (! array_key_exists($this->days, self::RANGES)) {
            $this->days = 7;
        }
    }

    public function setDays(int $days): void
    {
        $this->days = array_key_exists($days, self::RANGES) ? $days : 7;
    }

    /**
     * Everything the view prints, worked out in one place.
     *
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $insights = VisitorInsights::forDays($this->days);

        return [
            'ranges' => self::RANGES,
            'rangeLabel' => $insights->rangeLabel(),
            'hasData' => $insights->hasData(),
            'totals' => $insights->totals(),
            'daily' => $insights->daily(),
            'devices' => $insights->devices(),
            'topCategories' => $insights->topCategories(),
            'topSubcategories' => $insights->topSubcategories(),
            'topProducts' => $insights->topProducts(),
            'topInquiryProducts' => $insights->topInquiryProducts(),
            'topInquiryCategories' => $insights->topInquiryCategories(),
            'inquiryTypes' => $insights->inquiryTypes(),
            'topPages' => $insights->topPages(),
            'topReferrers' => $insights->topReferrers(),
        ];
    }
}
