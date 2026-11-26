<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_popups', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('eyebrow')->nullable();
            $table->string('headline')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();

            $table->string('cta_text')->nullable();
            // Either an internal path / external URL, or blank to open the
            // storefront's own enquiry modal instead.
            $table->string('cta_url')->nullable();
            $table->boolean('cta_opens_enquiry')->default(false);

            // 'all', or a JSON list of page names matching the Inertia
            // component names (Home, Products, ...).
            $table->string('audience')->default('all');
            $table->json('pages')->nullable();

            // once_per_visit | once_per_session | once_per_day | always
            $table->string('frequency')->default('once_per_session');
            $table->unsignedSmallInteger('delay_seconds')->default(3);

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('expire_at')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'publish_at', 'expire_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_popups');
    }
};
