<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reels now come from YouTube Shorts instead of Instagram.
     *
     * The whole reason for the switch: YouTube's embed plays inline on any
     * site, while Instagram's always bounces the viewer to instagram.com. That
     * also removes the need to host the file ourselves, so the uploaded-video
     * column goes with it, and YouTube supplies a thumbnail for free.
     */
    public function up(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->string('youtube_url')->nullable()->after('title');
            $table->dropColumn(['video', 'reel_url']);
        });
    }

    public function down(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->string('video')->nullable()->after('title');
            $table->string('reel_url')->nullable()->after('video');
            $table->dropColumn('youtube_url');
        });
    }
};
