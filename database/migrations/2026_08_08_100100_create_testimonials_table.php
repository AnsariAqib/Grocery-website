<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The home page's client reviews, which were four cards written into the Vue
 * component. Same shape as the card: a quote, who said it, and the pill that
 * says which side of the business they came from.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('quote');
            // Whole stars, 1–5; the card draws that many.
            $table->unsignedTinyInteger('rating')->default(5);
            // The little green-ticked pill: "Corporate", "Retail Customer"…
            $table->string('tag')->nullable();
            // Font Awesome name, picked from the same list as the other icons.
            $table->string('icon')->default('fa-user-tie');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
