<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // The supplier's own code for the item. Nullable because products
            // typed into the admin by hand do not have one, unique because the
            // bulk importer matches on it to update rather than duplicate.
            $table->string('sku')->nullable()->unique()->after('slug');

            // Free text the catalogue search also looks through, so an item can
            // be found by words that are not in its name ("phenyl", "jharu").
            $table->text('search_keywords')->nullable()->after('description');

            // Up to three photos for the card's slider. `image` stays as the
            // single legacy column — it is what the home page rail reads, and
            // it is the last fallback when these are empty.
            $table->json('images')->nullable()->after('image');

            // [{ sku, size, price, price_on_request, images: [] }]
            // One row per pack size. Kept as JSON rather than its own table
            // because the catalogue is filtered entirely client-side, so these
            // are never queried on their own.
            $table->json('variants')->nullable()->after('pack_size');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'search_keywords', 'images', 'variants']);
        });
    }
};
