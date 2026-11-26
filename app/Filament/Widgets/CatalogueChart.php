<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

/**
 * Products per store category.
 *
 * Horizontal bars: the job is comparing magnitude across ten long category
 * names, which a vertical axis cannot label without rotating text. One series,
 * so one hue and no legend — the heading names the measure. The Categories
 * table carries the same numbers as the accessible table view.
 */
class CatalogueChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Products per category';

    protected ?string $description = 'Store categories only — B2B categories are enquiry-led and hold no products.';

    protected ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $categories = Category::query()
            ->where('type', Category::TYPE_STORE)
            ->withCount(['products' => fn ($query) => $query->where('products.is_active', true)])
            ->orderByDesc('products_count')
            ->orderBy('sort_order')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data' => $categories->pluck('products_count')->all(),
                    'backgroundColor' => '#E4652E',
                    'hoverBackgroundColor' => '#C9541F',
                    // 4px rounded data-end, square against the baseline.
                    'borderRadius' => ['topLeft' => 0, 'bottomLeft' => 0, 'topRight' => 4, 'bottomRight' => 4],
                    'borderSkipped' => false,
                    'barPercentage' => 0.72,
                    'categoryPercentage' => 0.78,
                ],
            ],
            'labels' => $categories->pluck('name')->all(),
        ];
    }

    protected function getOptions(): RawJs
    {
        // Horizontal orientation, recessive grid, integer ticks, and the default
        // per-bar tooltip kept as the hover layer.
        return RawJs::make(<<<'JS'
            {
                indexAxis: 'y',
                maintainAspectRatio: false,
                layout: { padding: { right: 8 } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#061427',
                        padding: 10,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                const n = context.parsed.x;
                                return n === 1 ? '1 product' : n + ' products';
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: 'rgba(6, 20, 39, 0.06)', drawTicks: false },
                        ticks: {
                            precision: 0,
                            color: '#94A3B8',
                            font: { size: 11 },
                            padding: 6,
                        },
                    },
                    y: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            color: '#475569',
                            font: { size: 12, weight: '600' },
                            crossAlign: 'far',
                        },
                    },
                },
            }
        JS);
    }
}
