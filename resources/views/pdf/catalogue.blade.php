{{--
    The printed catalogue.

    Written for dompdf, which is not a browser: no flexbox, no grid, no CSS
    variables, no webfonts beyond the core ones. The layout is therefore tables
    and floats on purpose — a nicer stylesheet would render as a blank page.

    Everything variable comes from the admin's form (see ProductCataloguePage),
    so the same template prints a retail price list, a B2B supply list, or one
    category on its own.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 96px 34px 64px 34px; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5px;
            color: #1E293B;
            margin: 0;
        }

        /* Repeated on every page by dompdf because they are fixed. */
        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 56px;
        }

        footer {
            position: fixed;
            bottom: -44px;
            left: 0;
            right: 0;
            height: 30px;
            color: #94A3B8;
            font-size: 8px;
        }

        .head-bar { width: 100%; border-collapse: collapse; }
        .head-bar td { vertical-align: middle; padding: 0; }
        .head-logo { height: 34px; }
        .head-name { font-size: 12.5px; font-weight: bold; color: #061427; }
        .head-sub { font-size: 8px; color: #64748B; }
        .head-right { text-align: right; font-size: 8px; color: #64748B; }
        .rule { height: 3px; background: #E4652E; margin-top: 8px; }

        .foot-bar { width: 100%; border-collapse: collapse; }
        .foot-bar td { padding: 0; border-top: 1px solid #E2E8F0; padding-top: 6px; }
        .foot-right { text-align: right; }

        /* ---- Cover ---- */
        .cover { text-align: center; padding-top: 130px; }
        .cover-logo { height: 92px; margin-bottom: 26px; }
        .cover-kicker {
            font-size: 10px;
            letter-spacing: 3px;
            color: #E4652E;
            font-weight: bold;
            text-transform: uppercase;
        }
        .cover-title {
            font-size: 34px;
            font-weight: bold;
            color: #061427;
            margin: 14px 60px 10px 60px;
            line-height: 1.2;
        }
        .cover-sub { font-size: 12px; color: #475569; margin: 0 80px; line-height: 1.6; }
        .cover-line { width: 90px; height: 4px; background: #E4652E; margin: 26px auto; }
        .cover-meta { font-size: 9.5px; color: #64748B; line-height: 1.9; }
        .cover-strip {
            margin: 34px 40px 0 40px;
            background: #061427;
            color: #FFFFFF;
            padding: 14px 18px;
            font-size: 9px;
            line-height: 1.7;
        }
        .cover-strip strong { color: #FFB38A; }

        /* ---- Category sections ---- */
        .cat-head {
            background: #061427;
            color: #FFFFFF;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 16px;
        }
        .cat-head .count { float: right; font-size: 8.5px; color: #FFB38A; font-weight: normal; }
        .sub-head {
            color: #E4652E;
            font-size: 10px;
            font-weight: bold;
            margin: 12px 0 4px 0;
            border-bottom: 1px solid #F1D5C6;
            padding-bottom: 3px;
        }

        table.items { width: 100%; border-collapse: collapse; }
        table.items th {
            background: #F1F5F9;
            color: #475569;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            padding: 5px 8px;
            border-bottom: 1px solid #E2E8F0;
        }
        table.items td {
            padding: 6px 8px;
            border-bottom: 1px solid #EEF2F7;
            vertical-align: top;
        }
        table.items tr { page-break-inside: avoid; }
        .col-sku { width: 68px; color: #94A3B8; font-size: 8px; }
        .col-brand { width: 92px; color: #64748B; }
        .col-pack { width: 96px; color: #475569; }
        .col-price { width: 74px; text-align: right; font-weight: bold; color: #061427; }
        .name { font-weight: bold; color: #0F172A; }
        .desc { color: #64748B; font-size: 8px; margin-top: 2px; }
        .on-request { color: #E4652E; font-weight: bold; font-size: 8.5px; }
        .sizes { color: #475569; font-size: 8px; }
        .col-img { width: 46px; padding-right: 0; }
        .thumb {
            width: 40px;
            height: 40px;
            border: 1px solid #E2E8F0;
            border-collapse: collapse;
            background: #FFFFFF;
        }
        .thumb td { padding: 0; border: 0; text-align: center; vertical-align: middle; }
        .thumb img { max-width: 38px; max-height: 38px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<header>
    <table class="head-bar">
        <tr>
            <td width="44">
                @if ($logo)
                    <img src="{{ $logo }}" class="head-logo" alt="">
                @endif
            </td>
            <td>
                <div class="head-name">{{ $business }}</div>
                <div class="head-sub">{{ $title }}</div>
            </td>
            <td class="head-right">
                {{ $printedOn }}
            </td>
        </tr>
    </table>
    <div class="rule"></div>
</header>

<footer>
    <table class="foot-bar">
        <tr>
            <td>{{ $business }} &nbsp;·&nbsp; {{ $address }}</td>
            <td class="foot-right">{{ $phone }}</td>
        </tr>
    </table>
</footer>

{{-- ---------------------------------------------------------------- cover --}}
<div class="cover">
    @if ($logo)
        <img src="{{ $logo }}" class="cover-logo" alt="">
    @endif

    <div class="cover-kicker">{{ $kicker }}</div>
    <div class="cover-title">{{ $title }}</div>

    @if ($subtitle)
        <p class="cover-sub">{{ $subtitle }}</p>
    @endif

    <div class="cover-line"></div>

    <div class="cover-meta">
        {{ $productCount }} products &nbsp;·&nbsp; {{ $categoryCount }} categories<br>
        Prices and stock as on {{ $printedOn }}
    </div>

    <div class="cover-strip">
        <strong>{{ $business }}</strong><br>
        {{ $address }}<br>
        {{ $phone }} @if ($email) &nbsp;·&nbsp; {{ $email }} @endif<br>
        {{ $hours }}
    </div>
</div>

<div class="page-break"></div>

{{-- ------------------------------------------------------------- contents --}}
@foreach ($groups as $group)
    <div class="cat-head">
        {{ $group['name'] }}
        <span class="count">{{ count($group['items']) }} items</span>
    </div>

    @foreach ($group['subgroups'] as $subgroup)
        <div class="sub-head">{{ $subgroup['name'] }}</div>

        <table class="items">
            <thead>
                <tr>
                    @if ($showSku)<th class="col-sku">Code</th>@endif
                    @if ($showImages)<th class="col-img"></th>@endif
                    <th>Product</th>
                    <th class="col-brand">Brand</th>
                    <th class="col-pack">Pack</th>
                    @if ($showPrices)<th class="col-price">Price</th>@endif
                </tr>
            </thead>
            <tbody>
                @foreach ($subgroup['items'] as $item)
                    <tr>
                        @if ($showSku)<td class="col-sku">{{ $item['sku'] ?: '—' }}</td>@endif
                        @if ($showImages)
                            <td class="col-img">
                                {{-- A one-cell table centres the photo; dompdf ignores flexbox. --}}
                                <table class="thumb"><tr><td>
                                    @if ($item['image'])
                                        <img src="{{ $item['image'] }}" alt="">
                                    @endif
                                </td></tr></table>
                            </td>
                        @endif
                        <td>
                            <div class="name">{{ $item['name'] }}</div>
                            @if ($showDescriptions && $item['description'])
                                <div class="desc">{{ $item['description'] }}</div>
                            @endif
                            @if (count($item['sizes']) > 1)
                                <div class="sizes">Sizes: {{ implode(' · ', $item['sizes']) }}</div>
                            @endif
                        </td>
                        <td class="col-brand">{{ $item['brand'] ?: '—' }}</td>
                        <td class="col-pack">{{ $item['pack'] ?: '—' }}</td>
                        @if ($showPrices)
                            <td class="col-price">
                                @if ($item['onRequest'])
                                    <span class="on-request">On request</span>
                                @else
                                    {{ $item['price'] ?: '—' }}
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endforeach

</body>
</html>
