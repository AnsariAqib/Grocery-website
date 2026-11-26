<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per storefront page a visitor opens.
 *
 * Enough to answer the questions the shop actually asks — how many people came
 * today, which categories they went into, which pack they opened — without a
 * third-party script on the site.
 *
 * Nobody is identified. `visitor_hash` is the day's date mixed with the IP and
 * browser and run through a one-way hash, so the same person counts once a day
 * and cannot be traced back or followed across days.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();

            // Which Inertia page, so a Home view and a Product view can be told
            // apart without parsing paths back out later.
            $table->string('page', 40)->index();
            $table->string('path', 255);

            // Filled where the page is about one of them, so "most looked at
            // category" is a group-by rather than a guess from the URL.
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->char('visitor_hash', 32)->index();
            $table->boolean('is_mobile')->default(false);
            // Host only ("google.com"), never the full referring URL.
            $table->string('referrer_host', 120)->nullable();

            $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
