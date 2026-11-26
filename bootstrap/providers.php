<?php

use App\Providers\Filament\AdminPanelProvider;
use Barryvdh\DomPDF\ServiceProvider;

return array_values(array_filter([
    AdminPanelProvider::class,

    // Named here rather than left to package discovery. This project's
    // composer.json runs only `filament:upgrade` after an install — there is no
    // `package:discover` — so bootstrap/cache/packages.php is only ever rebuilt
    // when it is missing, and on the server it is a tracked file nobody can
    // delete. A package added to composer.json would therefore never be
    // registered there, which is why the catalogue PDF answered with "Target
    // class [dompdf.wrapper] does not exist". Listed here it works off the lock
    // file alone; if discovery does find it too, Application::register() hands
    // back the instance it already made rather than registering it twice.
    //
    // Guarded, because a provider that is named but not installed takes the
    // whole site down with it. Absent, the panel's catalogue page says so in
    // plain words instead.
    class_exists(ServiceProvider::class) ? ServiceProvider::class : null,
]));
