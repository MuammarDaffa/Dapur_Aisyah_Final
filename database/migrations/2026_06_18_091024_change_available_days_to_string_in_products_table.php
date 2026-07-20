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
        Schema::table('produk', function (Blueprint $table) {
            $table->string('available_days')->nullable()->change();
        });

        // 2. Data migration: get all produk, extract the first day from the stringified array
        $produk = \DB::table('produk')->get();
        foreach ($produk as $produk) {
            // The value is now a string like '["senin", "selasa"]', decode it to array to extract
            $days = json_decode($produk->available_days, true);
            $firstDay = is_array($days) && count($days) > 0 ? $days[0] : 'senin';
            // update as simple string since column is now a string
            \DB::table('produk')->where('id', $produk->id)->update([
                'available_days' => $firstDay
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->json('available_days')->nullable()->change();
        });

        // Convert string back to array
        $produk = \DB::table('produk')->get();
        foreach ($produk as $produk) {
            \DB::table('produk')->where('id', $produk->id)->update([
                'available_days' => json_encode([$produk->available_days])
            ]);
        }
    }
};
