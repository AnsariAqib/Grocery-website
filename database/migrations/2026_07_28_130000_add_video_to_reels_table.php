<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Instagram's public embed will not play a reel in place — it always sends
     * the viewer to instagram.com — so the video has to be self-hosted for the
     * player to work on our own page. The Instagram link stays, but only as the
     * "View on Instagram" companion link.
     */
    public function up(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->string('video')->nullable()->after('title');
        });

        Schema::table('reels', function (Blueprint $table) {
            $table->string('reel_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->dropColumn('video');
        });
    }
};
