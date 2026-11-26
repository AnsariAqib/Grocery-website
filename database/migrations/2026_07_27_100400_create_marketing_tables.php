<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('badge')->nullable();
            $table->string('badge_bg')->nullable();
            $table->string('discount')->nullable();
            // Falls back to badge_bg when empty; only the dark card differs.
            $table->string('discount_color')->nullable();
            $table->string('subtitle')->nullable();
            // Short copy used by the home cards; the /promotions cards use subtitle.
            $table->text('description')->nullable();
            $table->string('validity')->nullable();
            $table->string('image')->nullable();
            $table->string('card_bg')->nullable();
            $table->string('border_color')->nullable();
            // Switches the whole card to the navy treatment (white title, muted
            // subtitle, translucent divider) instead of six colour columns.
            $table->boolean('is_dark')->default(false);
            $table->string('group')->default('latest'); // latest | festive | seasonal | bulk
            $table->string('cta_text')->nullable();
            $table->boolean('show_on_home')->default(false);
            $table->boolean('show_on_page')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['show_on_home', 'sort_order']);
            $table->index(['group', 'sort_order']);
        });

        Schema::create('logos', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('client'); // client | brand
            $table->string('name');
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'sort_order']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('title')->nullable();      // e.g. AUTHORISED DISTRIBUTOR
            $table->text('description')->nullable();
            $table->string('signatory')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('pdf_file')->nullable();   // optional certificate PDF
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('logos');
        Schema::dropIfExists('promotions');
    }
};
