<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\CatalogueBuilder;
use Illuminate\Console\Command;

/**
 * Shrinks every product photo for the catalogue PDF ahead of time.
 *
 * The catalogue builder makes these on demand and keeps them, but the first
 * run on a large catalogue has hundreds of photos to shrink. Running this from
 * the shell (and again after a big photo import) means the admin's click only
 * typesets.
 *
 * It stops after --time seconds because the hosting panel's web terminal is
 * itself an HTTP request and gives up on long commands; everything made so far
 * is kept, so running it again carries on.
 */
class WarmCatalogueThumbnails extends Command
{
    protected $signature = 'catalogue:thumbnails
        {--time=40 : Stop after this many seconds; run again to continue}';

    protected $description = 'Pre-generate the product thumbnails printed in the catalogue PDF';

    public function handle(CatalogueBuilder $builder): int
    {
        $builder->limitThumbnailTime((float) $this->option('time'));

        $ready = 0;

        Product::query()->lazyById(100)->each(function (Product $product) use ($builder, &$ready) {
            if ($builder->thumbnail($product) !== null) {
                $ready++;
            }
        });

        $pending = $builder->pendingThumbnails();

        $this->info($ready.' products have a thumbnail ready.');

        if ($pending > 0) {
            $this->warn($pending.' still to do — run the command again.');
        } else {
            $this->info('All done.');
        }

        return self::SUCCESS;
    }
}
