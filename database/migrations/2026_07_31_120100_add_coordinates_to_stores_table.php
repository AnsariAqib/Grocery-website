<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // Exact pin. 7 decimals is ~1 cm, far finer than a shopfront needs.
            $table->decimal('latitude', 10, 7)->nullable()->after('map_query');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            // A Google Maps link pasted from the app ("Share → Copy link").
            // Used as-is for "View on Map" so short links keep working.
            $table->string('map_url')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'map_url']);
        });
    }
};
