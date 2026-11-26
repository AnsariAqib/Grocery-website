<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Featured flags drive the two new home page showcases.
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active');
        });

        // Photo rail shown beside the featured sections.
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Editable USP cards on the two category pages. `scope` picks the page:
        // store | b2b | both.
        Schema::create('usp_cards', function (Blueprint $table) {
            $table->id();
            $table->string('scope')->default('both');
            $table->string('icon')->default('fa-shield-halved');
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['scope', 'sort_order']);
        });

        // Steps of the B2B order process strip.
        Schema::create('order_steps', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->default('fa-list-check');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_steps');
        Schema::dropIfExists('usp_cards');
        Schema::dropIfExists('gallery_images');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
