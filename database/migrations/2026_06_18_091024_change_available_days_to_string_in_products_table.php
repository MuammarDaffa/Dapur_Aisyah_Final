<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Schema change: change column type to string
        Schema::table('products', function (Blueprint $table) {
            $table->string('available_days')->nullable()->change();
        });

        // 2. Data migration: get all products, extract the first day from the stringified array
        $products = \DB::table('products')->get();
        foreach ($products as $product) {
            // The value is now a string like '["senin", "selasa"]', decode it to array to extract
            $days = json_decode($product->available_days, true);
            $firstDay = is_array($days) && count($days) > 0 ? $days[0] : 'senin';
            // update as simple string since column is now a string
            \DB::table('products')->where('id', $product->id)->update([
                'available_days' => $firstDay
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('available_days')->nullable()->change();
        });

        // Convert string back to array
        $products = \DB::table('products')->get();
        foreach ($products as $product) {
            \DB::table('products')->where('id', $product->id)->update([
                'available_days' => json_encode([$product->available_days])
            ]);
        }
    }
};
