<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // One short line under the product name on the catalogue card.
            $table->string('description')->nullable()->after('brand');
            // When set, the card shows "Price on Request" instead of `price`.
            // The typed price is kept either way so switching back is lossless.
            $table->boolean('price_on_request')->default(false)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['description', 'price_on_request']);
        });
    }
};
