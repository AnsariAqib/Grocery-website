<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The hero button labels used to be rendered with v-html, so the arrow icon
     * was baked into the text itself. Once btn_primary_has_arrow / btn_navy_has_arrow
     * arrived, HeroSlider.vue switched to escaped {{ }} output plus its own <i>,
     * which left every pre-existing row printing its markup literally on the page.
     * Strip it out of the rows that were seeded or saved before that change.
     */
    public function up(): void
    {
        DB::table('hero_slides')
            ->select('id', 'btn_primary_text', 'btn_navy_text')
            ->orderBy('id')
            ->each(function (object $slide) {
                $clean = static fn (?string $text) => $text === null
                    ? null
                    : trim(preg_replace('/\s*<i\b[^>]*>\s*<\/i>/i', '', $text));

                $update = array_filter([
                    'btn_primary_text' => $clean($slide->btn_primary_text),
                    'btn_navy_text' => $clean($slide->btn_navy_text),
                ], fn ($value, $column) => $value !== $slide->{$column}, ARRAY_FILTER_USE_BOTH);

                if ($update !== []) {
                    DB::table('hero_slides')->where('id', $slide->id)->update($update);
                }
            });
    }

    /**
     * Irreversible: the original icon markup is not recoverable, and the arrow
     * is now driven by the btn_*_has_arrow columns instead.
     */
    public function down(): void
    {
        //
    }
};
