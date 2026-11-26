<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            // The raw link pasted from Instagram's Share menu. The embed URL is
            // derived from it at read time rather than stored, so a tracking
            // suffix (?igsh=…) on the pasted link never has to be cleaned up.
            $table->string('reel_url');
            // Instagram will not hand out a thumbnail without an API token, so
            // the poster frame is uploaded by hand. Optional: the card falls
            // back to a branded placeholder.
            $table->string('cover_image')->nullable();
            $table->text('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
