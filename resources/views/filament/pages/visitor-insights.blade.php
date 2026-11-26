@php
    $tiles = [
        ['key' => 'visitors', 'label' => 'Visitors', 'hint' => 'People, counted once a day each'],
        ['key' => 'views', 'label' => 'Pages opened', 'hint' => 'Every page load'],
        ['key' => 'productViews', 'label' => 'Product pages opened', 'hint' => 'A single product looked at'],
        ['key' => 'inquiries', 'label' => 'Enquiries', 'hint' => 'Forms actually submitted'],
        ['key' => 'pagesPerVisit', 'label' => 'Pages per visitor', 'hint' => 'How far people go in'],
    ];

    $peak = 0;

    foreach ($daily as $day) {
        $peak = max($peak, $day['views']);
    }

    // A year of bars would be unreadable at one bar a day, so long ranges are
    // shown a week at a time. The totals above are untouched either way.
    $bars = $daily;

    if (count($bars) > 92) {
        $bars = collect($daily)
            ->chunk(7)
            ->map(fn ($week) => [
                'label' => $week->first()['label'],
                'views' => $week->sum('views'),
                'visitors' => $week->sum('visitors'),
            ])
            ->values()
            ->all();

        $peak = 0;

        foreach ($bars as $bar) {
            $peak = max($peak, $bar['views']);
        }
    }
@endphp

<x-filament-panels::page>
    {{-- Range --}}
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
            @foreach ($ranges as $value => $label)
                <button type="button" wire:click="setDays({{ $value }})"
                    style="border-radius: 8px; padding: 0.4375rem 0.875rem; font-size: 0.8125rem; font-weight: 600; border: 1px solid {{ $days === $value ? '#FA5400' : '#e2e8f0' }}; background: {{ $days === $value ? '#FA5400' : '#fff' }}; color: {{ $days === $value ? '#fff' : '#334155' }};">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <span style="font-size: 0.75rem; color: #64748b;">{{ $rangeLabel }}</span>
    </div>

    @if (! $hasData)
        <div style="background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%); border: 1px solid #fed7aa; border-radius: 12px; padding: 1.25rem 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a;">Counting has just started</h3>
            <p style="font-size: 0.8125rem; color: #334155; line-height: 1.7; margin-top: 0.375rem;">
                Nothing has been recorded yet. Every page a visitor opens on the website from now on is counted here — come back tomorrow and this page will have a day of numbers on it.
            </p>
        </div>
    @endif

    {{-- Headline figures --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.875rem;">
        @foreach ($tiles as $tile)
            @php
                $figure = $totals[$tile['key']];
                $value = $figure['value'];
                $previous = $figure['previous'];
                // Below a handful, a percentage says more about the small
                // number than about the trend — "up 27000%" from six visits is
                // noise, so the comparison is simply left off.
                $change = $previous >= 5 ? (int) round(($value - $previous) / $previous * 100) : null;
            @endphp

            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem 1.125rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
                <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: #64748b;">{{ $tile['label'] }}</div>

                <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.375rem;">
                    <span style="font-size: 1.75rem; font-weight: 800; color: #0f172a; line-height: 1;">
                        {{ is_float($value) ? number_format($value, 1) : number_format($value) }}
                    </span>

                    @if ($change !== null)
                        <span style="font-size: 0.75rem; font-weight: 700; color: {{ $change >= 0 ? '#16a34a' : '#dc2626' }};">
                            {{ $change >= 0 ? '▲' : '▼' }} {{ min(abs($change), 999) }}%
                        </span>
                    @endif
                </div>

                <div style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.375rem;">
                    {{ $tile['hint'] }}@if ($change !== null) · was {{ is_float($previous) ? number_format($previous, 1) : number_format($previous) }} before @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Day by day --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
        <div style="display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 0.9375rem; font-weight: 800; color: #0f172a;">Day by day</h3>
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">
                    {{ count($bars) === count($daily) ? 'One bar per day.' : 'One bar per week — the range is too long for daily bars.' }}
                    The solid part is visitors, the lighter part the extra pages they opened.
                </p>
            </div>

            <div style="display: flex; gap: 0.875rem; font-size: 0.6875rem; color: #64748b;">
                <span style="display: inline-flex; align-items: center; gap: 0.3125rem;"><span style="width: 10px; height: 10px; border-radius: 3px; background: #FA5400;"></span> Visitors</span>
                <span style="display: inline-flex; align-items: center; gap: 0.3125rem;"><span style="width: 10px; height: 10px; border-radius: 3px; background: #fed7aa;"></span> Pages</span>
            </div>
        </div>

        <div style="margin-top: 1rem; overflow-x: auto;">
            <div style="display: flex; align-items: flex-end; gap: 3px; height: 180px; min-width: {{ max(320, count($bars) * 10) }}px;">
                @foreach ($bars as $bar)
                    @php
                        $height = $peak > 0 ? max(2, round($bar['views'] / $peak * 100)) : 2;
                        $share = $bar['views'] > 0 ? round($bar['visitors'] / $bar['views'] * 100) : 0;
                    @endphp

                    <div title="{{ $bar['label'] }} — {{ $bar['visitors'] }} visitors, {{ $bar['views'] }} pages"
                        style="flex: 1 1 0; min-width: 5px; height: {{ $height }}%; border-radius: 4px 4px 0 0; background: #fed7aa; display: flex; align-items: flex-end;">
                        <div style="width: 100%; height: {{ $share }}%; border-radius: {{ $share > 92 ? '4px 4px 0 0' : '0' }}; background: #FA5400;"></div>
                    </div>
                @endforeach
            </div>

            @if (count($bars) > 0)
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.6875rem; color: #94a3b8;">
                    <span>{{ $bars[0]['label'] }}</span>
                    <span>{{ $bars[count($bars) - 1]['label'] }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- What they looked at --}}
    <div>
        <h2 style="font-size: 1.0625rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem;">What people looked at</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 0.875rem; align-items: start;">
            @include('filament.pages.partials.insight-list', [
                'title' => 'Most visited categories',
                'subtitle' => 'A category page and any product inside it both count.',
                'rows' => $topCategories,
                'valueKey' => 'views',
                'valueLabel' => 'views',
                'secondKey' => 'visitors',
                'secondLabel' => 'people',
                'empty' => 'No category pages were opened in this stretch.',
            ])

            @include('filament.pages.partials.insight-list', [
                'title' => 'Most opened sub-categories',
                'subtitle' => 'Which shelf inside a category people go to.',
                'rows' => $topSubcategories,
                'valueKey' => 'views',
                'valueLabel' => 'views',
                'secondKey' => 'visitors',
                'secondLabel' => 'people',
                'empty' => 'Nobody has filtered down to a sub-category yet.',
            ])

            @include('filament.pages.partials.insight-list', [
                'title' => 'Most viewed products',
                'subtitle' => 'Product pages opened on their own.',
                'rows' => $topProducts,
                'valueKey' => 'views',
                'valueLabel' => 'views',
                'secondKey' => 'visitors',
                'secondLabel' => 'people',
                'empty' => 'No product pages were opened in this stretch.',
            ])
        </div>
    </div>

    {{-- What they asked for --}}
    <div>
        <h2 style="font-size: 1.0625rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem;">What people asked for</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 0.875rem; align-items: start;">
            @include('filament.pages.partials.insight-list', [
                'title' => 'Most enquired products',
                'subtitle' => 'Taken from the product named on the enquiry form.',
                'rows' => $topInquiryProducts,
                'valueKey' => 'count',
                'valueLabel' => 'enquiries',
                'empty' => 'No enquiries named a product in this stretch.',
            ])

            @include('filament.pages.partials.insight-list', [
                'title' => 'Most enquired categories',
                'subtitle' => 'The category chosen on the bulk enquiry form.',
                'rows' => $topInquiryCategories,
                'valueKey' => 'count',
                'valueLabel' => 'enquiries',
                'empty' => 'No enquiries chose a category in this stretch.',
            ])

            @include('filament.pages.partials.insight-list', [
                'title' => 'Kind of enquiry',
                'subtitle' => 'Which side of the business is being asked.',
                'rows' => $inquiryTypes,
                'valueKey' => 'count',
                'valueLabel' => 'enquiries',
                'empty' => 'No enquiries in this stretch.',
            ])
        </div>
    </div>

    {{-- How they got here --}}
    <div>
        <h2 style="font-size: 1.0625rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem;">How people got here</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 0.875rem; align-items: start;">
            @include('filament.pages.partials.insight-list', [
                'title' => 'Pages of the site',
                'subtitle' => 'Where the traffic goes overall.',
                'rows' => $topPages,
                'valueKey' => 'views',
                'valueLabel' => 'views',
                'secondKey' => 'visitors',
                'secondLabel' => 'people',
                'empty' => 'No page loads recorded in this stretch.',
            ])

            @include('filament.pages.partials.insight-list', [
                'title' => 'Where visitors came from',
                'subtitle' => 'The site they arrived from. Direct is a typed address, a bookmark or a WhatsApp forward.',
                'rows' => $topReferrers,
                'valueKey' => 'visitors',
                'valueLabel' => 'people',
                'empty' => 'No visits recorded in this stretch.',
            ])

            {{-- Phone against desktop --}}
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
                <h3 style="font-size: 0.9375rem; font-weight: 800; color: #0f172a;">Phone or computer</h3>
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">Share of visitors, worked out from the browser they used.</p>

                <div style="display: flex; height: 14px; border-radius: 999px; overflow: hidden; background: #f1f5f9; margin-top: 0.875rem;">
                    <div style="width: {{ $devices['mobileShare'] }}%; background: #FA5400;"></div>
                    <div style="width: {{ 100 - $devices['mobileShare'] }}%; background: #cbd5e1;"></div>
                </div>

                <div style="display: grid; gap: 0.375rem; margin-top: 0.75rem; font-size: 0.8125rem; color: #334155;">
                    <span><strong style="color: #0f172a;">{{ $devices['mobileShare'] }}%</strong> on a phone — {{ number_format($devices['mobile']) }} {{ Str::plural('visitor', $devices['mobile']) }}</span>
                    <span><strong style="color: #0f172a;">{{ 100 - $devices['mobileShare'] }}%</strong> on a computer — {{ number_format($devices['desktop']) }} {{ Str::plural('visitor', $devices['desktop']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <p style="font-size: 0.6875rem; color: #94a3b8; line-height: 1.7;">
        Counted by the website itself — no outside tracker, no cookies, and nothing that identifies a visitor is stored. The same person is counted once per day, and rows older than a year are cleared out automatically.
    </p>
</x-filament-panels::page>
