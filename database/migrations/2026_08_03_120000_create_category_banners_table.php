<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_banners', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // 'store', 'b2b'
            $table->string('badge_text')->default('Catalogue');
            $table->string('badge_icon')->default('fa-layer-group');
            $table->string('title');
            $table->string('highlight');
            $table->text('subtitle')->nullable();
            $table->string('cta_text')->nullable()->default('Browse All Products');
            $table->string('cta_url')->nullable()->default('/products');
            $table->string('background_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial default records for both Store and B2B categories
        DB::table('category_banners')->insert([
            [
                'type' => 'store',
                'badge_text' => 'Retail Catalogue',
                'badge_icon' => 'fa-store',
                'title' => 'Everything Your',
                'highlight' => 'Home Needs',
                'subtitle' => 'Groceries, fresh produce, personal care and home essentials across every aisle of our supermarkets.',
                'cta_text' => 'Browse All Products',
                'cta_url' => '/products',
                'background_image' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'b2b',
                'badge_text' => 'Corporate Supply',
                'badge_icon' => 'fa-industry',
                'title' => 'Bulk Supply For',
                'highlight' => 'Every Business',
                'subtitle' => 'Canteen, housekeeping, office and industrial consumables — quoted with GST pricing and delivered to your site.',
                'cta_text' => 'Browse All B2B Products',
                'cta_url' => '/products?category=all',
                'background_image' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_banners');
    }
};
