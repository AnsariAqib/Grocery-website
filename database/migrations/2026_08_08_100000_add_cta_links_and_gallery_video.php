<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Three small additions that all point the same way — more of the page under
 * the admin's control:
 *
 * - The home page's two category panels and the promotion cards had a button
 *   label in the admin but nowhere to say where the button goes; every one of
 *   them opened the enquiry form. They get a link, and a blank link keeps the
 *   old behaviour.
 * - The gallery can hold a YouTube video as well as a photo. The link only —
 *   nothing is uploaded, and the video plays from YouTube.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_panels', function (Blueprint $table) {
            $table->string('cta_url')->nullable()->after('cta_text');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->string('cta_url')->nullable()->after('cta_text');
        });

        Schema::table('gallery_images', function (Blueprint $table) {
            // 'photo' | 'video' — a string rather than an enum so a third kind
            // does not need a migration on MySQL.
            $table->string('media_type', 16)->default('photo')->after('id');
            $table->string('youtube_url')->nullable()->after('image');
            // A video carries a link and nothing else: YouTube's own still
            // stands in for it unless the admin uploads a cover.
            $table->string('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('category_panels', fn (Blueprint $table) => $table->dropColumn('cta_url'));
        Schema::table('promotions', fn (Blueprint $table) => $table->dropColumn('cta_url'));
        Schema::table('gallery_images', fn (Blueprint $table) => $table->dropColumn(['media_type', 'youtube_url']));
    }
};
