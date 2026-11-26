<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The shop is called Vinayak Stores, not Vinayak Supermarket.
 *
 * Renaming it in the code only fixes the text the code prints. Most of what
 * the site says lives in the database — page titles, hero copy, promotions,
 * the popup, a customer review — and none of that would change on its own.
 * The panel has no find-and-replace and no way to run a command, so the
 * rename travels with the deploy instead.
 *
 * Text columns only, and only the ones holding copy: enquiries are left alone
 * because they are what a customer actually wrote.
 */
return new class extends Migration
{
    /** Table => the columns on it that hold prose. */
    private const TEXT_COLUMNS = [
        'settings' => ['value'],
        'hero_slides' => ['title_html', 'subtext', 'btn_primary_text', 'btn_navy_text'],
        'stats' => ['label'],
        'category_panels' => ['badge_text', 'heading', 'subheading', 'cta_text'],
        'categories' => ['name', 'home_label'],
        'subcategories' => ['name'],
        'products' => ['name', 'brand', 'badge', 'description', 'search_keywords'],
        'promotions' => ['title', 'badge', 'subtitle', 'description', 'validity', 'cta_text'],
        'logos' => ['name'],
        'certificates' => ['brand_name', 'title', 'description', 'signatory'],
        'stores' => ['name', 'type', 'address'],
        'reels' => ['title', 'caption'],
        'catalogues' => ['title', 'description'],
        'leaders' => ['name', 'company', 'description'],
        'usp_cards' => ['title', 'description'],
        'order_steps' => ['title', 'description'],
        'category_banners' => ['badge_text', 'title', 'highlight', 'subtitle', 'cta_text'],
        'promo_popups' => ['title', 'eyebrow', 'headline', 'body', 'cta_text'],
        'gallery_images' => ['title', 'caption'],
        'testimonials' => ['name', 'role', 'quote', 'tag'],
    ];

    private const OLD = 'Vinayak Supermarket';

    private const NEW = 'Vinayak Stores';

    public function up(): void
    {
        foreach (self::TEXT_COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                // REPLACE() reads the same in MySQL and SQLite, so this runs
                // on the server and on a local copy alike.
                DB::table($table)
                    ->where($column, 'like', '%'.self::OLD.'%')
                    ->update([
                        $column => DB::raw("REPLACE({$column}, '".self::OLD."', '".self::NEW."')"),
                    ]);
            }
        }

        // The settings map is cached forever and these rows were changed
        // underneath the model, so nothing would have cleared it — the site
        // would go on printing the old page title until someone happened to
        // save a setting in the panel.
        Setting::forgetCache();
    }

    public function down(): void
    {
        // Not reversed on purpose. Rolling back would rename every "Vinayak
        // Stores" in the database, including the ones that were always
        // written that way.
    }
};
