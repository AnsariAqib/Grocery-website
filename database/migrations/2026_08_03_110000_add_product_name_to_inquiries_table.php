<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('inquiry_type');
        });

        // Backfill product_name for existing inquiries whose messages contain "Product: ..."
        $inquiries = DB::table('inquiries')->whereNull('product_name')->get();
        foreach ($inquiries as $inquiry) {
            if (!empty($inquiry->message) && preg_match('/^Product:\s*(.+)$/m', $inquiry->message, $matches)) {
                $productName = trim($matches[1]);
                DB::table('inquiries')->where('id', $inquiry->id)->update([
                    'product_name' => $productName,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn('product_name');
        });
    }
};
