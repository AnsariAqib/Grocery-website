<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogues', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('retail'); // retail | b2b
            $table->string('title');
            $table->string('description')->nullable();
            // PDF on the public disk. Nullable so a row can be prepared before
            // the file exists — the storefront treats that as "no catalogue yet".
            $table->string('file')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogues');
    }
};
