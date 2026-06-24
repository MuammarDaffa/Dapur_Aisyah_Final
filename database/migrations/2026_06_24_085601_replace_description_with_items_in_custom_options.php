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
        // 1. Add `items` column
        Schema::table('custom_options', function (Blueprint $table) {
            $table->json('items')->nullable()->after('name');
        });

        // 2. Migrate existing data: split description by newline, map to array, json encode
        $options = DB::table('custom_options')
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->get();

        foreach ($options as $option) {
            // pecah string berdasarkan baris baru
            $lines = explode("\n", $option->description);
            $items = [];
            foreach ($lines as $line) {
                // hapus +, -, *, bullet, dan spasi
                $cleaned = preg_replace('/^[\+\-\*•]\s*/', '', trim($line));
                if (!empty($cleaned)) {
                    $items[] = $cleaned;
                }
            }

            if (!empty($items)) {
                DB::table('custom_options')
                    ->where('id', $option->id)
                    ->update(['items' => json_encode($items)]);
            }
        }

        // 3. Drop `description` column
        Schema::table('custom_options', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add `description` back
        Schema::table('custom_options', function (Blueprint $table) {
            $table->text('description')->nullable()->after('image');
        });

        // 2. Migrate data back
        $options = DB::table('custom_options')
            ->whereNotNull('items')
            ->get();

        foreach ($options as $option) {
            $items = json_decode($option->items, true);
            if (is_array($items) && !empty($items)) {
                $description = implode("\n", array_map(fn($item) => "+ " . $item, $items));
                DB::table('custom_options')
                    ->where('id', $option->id)
                    ->update(['description' => $description]);
            }
        }

        // 3. Drop `items`
        Schema::table('custom_options', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }
};
