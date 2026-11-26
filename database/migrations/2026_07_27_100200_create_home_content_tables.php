<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            // Rendered with v-html by HeroSlider.vue: may contain <br> and
            // <span class="hero-title-gradient shiny-text">.
            $table->text('title_html');
            $table->text('subtext')->nullable();
            $table->string('btn_primary_text')->nullable();
            $table->string('btn_primary_link')->nullable();
            $table->string('btn_navy_text')->nullable();
            $table->string('btn_navy_link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedInteger('target');
            $table->string('suffix')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // The two big cards in the home "categories" section: FOR HOMES / FOR BUSINESSES.
        Schema::create('category_panels', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // store | b2b
            $table->string('badge_text');
            $table->string('heading');
            $table->string('subheading')->nullable();
            // Transparent cut-out PNG artwork sitting on the card.
            $table->string('art_image')->nullable();
            $table->string('cta_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_panels');
        Schema::dropIfExists('stats');
        Schema::dropIfExists('hero_slides');
    }
};
