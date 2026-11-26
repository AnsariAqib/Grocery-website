<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Only the "public" disk is overridden here; every other disk is merged in
    | from the framework defaults.
    |
    | The framework default builds this URL as `APP_URL . '/storage'`, which
    | hardcodes a host and port into every uploaded-media URL. The admin panel
    | is browsed on more than one origin during development (Apache on :80 and
    | `artisan serve` on :8000), and Filament's file upload fetches the stored
    | file with `fetch()` to render its preview. A hardcoded origin that does
    | not match the page makes that a cross-origin request, CORS blocks it, and
    | the upload field spins on "Loading" forever — while plain <img> tags keep
    | working, since images are not CORS-restricted.
    |
    | A root-relative URL is same-origin by definition, so it works on whichever
    | host the app is served from. Set ASSET_URL to an absolute URL if the media
    | ever needs to be served from another domain or a CDN.
    |
    */

    'disks' => [

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim((string) env('ASSET_URL'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

    ],

];
