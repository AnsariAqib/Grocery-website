<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | The framework's own config/app.php hardcodes 'UTC' — it does not read
    | APP_TIMEZONE — so without this file the .env value is silently ignored
    | and every now() is 5h30m behind Surat. Scheduled promotions and popups
    | compare publish_at / expire_at against now(), and the admin types those
    | in local time, so the offset made offers linger past their expiry.
    |
    | Only the keys below override the framework defaults; everything else in
    | config/app.php is merged in from the framework.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),

];
