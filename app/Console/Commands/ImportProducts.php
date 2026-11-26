<?php

namespace App\Console\Commands;

use App\Services\ProductImporter;
use App\Services\ProductImportResult;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * The shell face of {@see ProductImporter}.
 *
 * The admin's Bulk Product Import page does the same job through the panel;
 * this stays for catalogues large enough that a web request would give up
 * first, and for running an import from a deploy script.
 */
class ImportProducts extends Command
{
    protected $signature = 'products:import
        {file : Path to the CSV export}
        {--dry-run : Report what would change without writing anything}';

    protected $description = 'Import products, pack sizes and photo links from the supplier CSV';

    public function handle(ProductImporter $importer): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run: nothing will be written.');
        }

        $bar = null;

        $importer->trackProgress(function (int $done, int $total) use (&$bar) {
            if ($bar === null) {
                $this->info($total.' products found in the sheet');
                $bar = $this->output->createProgressBar($total);
                $bar->start();
            }

            $bar->setProgress($done);
        });

        try {
            $result = $importer->import($this->argument('file'), $dryRun);
        } catch (\Throwable $e) {
            $this->finishBar($bar);
            $this->error('Import aborted, nothing was written: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->finishBar($bar);
        $this->report($result);

        return self::SUCCESS;
    }

    private function finishBar(?ProgressBar $bar): void
    {
        if ($bar !== null) {
            $bar->finish();
            $this->newLine(2);
        }
    }

    private function report(ProductImportResult $result): void
    {
        $this->info($result->rows.' rows → '.$result->groups.' products');

        $this->table(['', 'Count'], [
            ['Products created', $result->productsCreated],
            ['Products updated', $result->productsUpdated],
            ['Pack sizes', $result->variants],
            ['Rows skipped', $result->rowsSkipped],
        ]);

        if ($result->createdCategories !== []) {
            $this->newLine();
            $this->warn('New categories were created. Add an image and icon to each in the admin, or they will look bare on the category pages:');

            foreach ($result->createdCategories as $name) {
                $this->line('  • '.$name);
            }
        }

        if ($result->missingImages !== []) {
            $this->newLine();
            $this->warn(count($result->missingImages).' photos named in the sheet have not been uploaded yet:');

            foreach (array_slice($result->missingImages, 0, 20) as $name) {
                $this->line('  • '.$name);
            }

            if (count($result->missingImages) > 20) {
                $this->line('  … and '.(count($result->missingImages) - 20).' more');
            }

            $this->line('Upload them on the admin\'s Bulk Image Upload page, then run this again — products are matched by SKU, so nothing will be duplicated.');
        }

        if ($result->warnings !== []) {
            $this->newLine();
            $this->warn(count($result->warnings).' rows had problems:');

            foreach (array_slice($result->warnings, 0, 20) as $warning) {
                $this->line('  • '.$warning);
            }
        }
    }
}
