<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The admin form now edits the `images` gallery rather than the single
     * `image` column. Seeding the gallery from what is already there means an
     * existing product opens with its photo in the field instead of an empty
     * uploader, and the model's fallback never has to carry the site.
     */
    public function up(): void
    {
        DB::table('products')
            ->whereNull('images')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->orderBy('id')
            ->each(function ($product) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['images' => json_encode([$product->image])]);
            });
    }

    public function down(): void
    {
        // The gallery is a copy of `image`, so dropping it loses nothing.
        DB::table('products')->update(['images' => null]);
    }
};
