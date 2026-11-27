<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        // The pages are rendered in the browser, so a crawler or a link
        // preview only ever sees what PHP printed here. Seo::forPage() builds
        // it from the controller's own `meta` prop plus Site Settings.
        $seo = \App\Support\Seo::forPage($page, request());
    @endphp

    <title>VIZORA FOODS | Fresh &bull; Local &bull; Organic Groceries</title>
    <meta name="description" content="Fresh groceries, daily essentials and farm-fresh produce delivered directly from local farmers to your doorstep.">
    <meta name="robots" content="{{ $seo['robots'] }}">
    <link rel="canonical" href="{{ $seo['canonical'] }}">
    <meta name="theme-color" content="#14532D">
    @if ($seo['verification'])
        <meta name="google-site-verification" content="{{ $seo['verification'] }}">
    @endif

    <link rel="icon" href="{{ asset('assets/farmlokal-favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('assets/farmlokal-favicon.svg') }}">

    {{-- Link previews: WhatsApp, Facebook and LinkedIn read these, X reads the
         twitter: pair below. --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seo['siteName'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ $seo['canonical'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:image:alt" content="{{ $seo['siteName'] }}">
    <meta property="og:locale" content="en_IN">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">

    @foreach ($seo['jsonLd'] as $schema)
        {{-- JSON_HEX_TAG so no value can close this script element early. --}}
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endforeach

    @php
        // Typography is admin-controlled (Site Settings -> Typography). Both the
        // stylesheet request and the :root override below come from the same two
        // settings, and fall back to the shipped defaults if a row is missing.
        $headingFont = \App\Models\Setting::val('font_heading', \App\Support\FontOptions::DEFAULT_HEADING);
        $bodyFont = \App\Models\Setting::val('font_body', \App\Support\FontOptions::DEFAULT_BODY);

        $fontsUrl = \App\Support\FontOptions::googleUrl($headingFont, $bodyFont);
        $headingStack = \App\Support\FontOptions::stack($headingFont, \App\Support\FontOptions::DEFAULT_HEADING);
        $bodyStack = \App\Support\FontOptions::stack($bodyFont);
    @endphp

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontsUrl }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/style.css', 'resources/js/app.js'])

    {{-- After @vite so these win over the :root defaults in style.css. Printed
         raw because a font stack carries quotes that {{ }} would entity-encode
         and break; the values are FontOptions constants, never user input. --}}
    <style>
        :root {
            --font-heading: {!! $headingStack !!};
            --font-main: {!! $bodyStack !!};
            --font-body: {!! $bodyStack !!};
        }
    </style>

    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
