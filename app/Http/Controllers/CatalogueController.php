<?php

namespace App\Http\Controllers;

use App\Models\Catalogue;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CatalogueController extends Controller
{
    /**
     * Streams the current master catalogue for a download button.
     *
     * The storefront only ever calls this when the shared `catalogues` prop
     * says a file exists, so a 404 here means the row was unpublished or the
     * file removed between page load and click.
     */
    public function download(string $type): Response
    {
        abort_unless(array_key_exists($type, Catalogue::TYPES), 404);

        $catalogue = Catalogue::forType($type);

        abort_if($catalogue === null, 404);

        // Static paths ("/assets/…") and remote URLs are served as-is.
        if (str_starts_with($catalogue->file, '/') || str_starts_with($catalogue->file, 'http')) {
            return redirect()->away($catalogue->file_url);
        }

        $disk = Storage::disk('public');

        abort_unless($disk->exists($catalogue->file), 404);

        return $disk->download($catalogue->file, $catalogue->download_name);
    }
}
