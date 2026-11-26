<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('gst_number')->nullable()->after('phone_links');
            $table->string('email')->nullable()->after('gst_number');
            // Extra photos beyond `image`; the card rotates through them.
            $table->json('gallery')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['gst_number', 'email', 'gallery']);
        });
    }
};
