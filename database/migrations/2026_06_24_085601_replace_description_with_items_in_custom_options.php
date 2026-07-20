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
        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->json('items')->nullable()->after('name');
        });

        // 2. Migrate existing data: split deskripsi by newline, map to array, json encode
        $options = DB::table('opsi_kustom')
            ->whereNotNull('deskripsi')
            ->where('deskripsi', '!=', '')
            ->get();

        foreach ($options as $option) {
            // pecah string berdasarkan baris baru
            $lines = explode("\n", $option->deskripsi);
            $items = [];
            foreach ($lines as $line) {
                // hapus +, -, *, bullet, dan spasi
                $cleaned = preg_replace('/^[\+\-\*•]\s*/', '', trim($line));
                if (!empty($cleaned)) {
                    $items[] = $cleaned;
                }
            }

            if (!empty($items)) {
                DB::table('opsi_kustom')
                    ->where('id', $option->id)
                    ->update(['items' => json_encode($items)]);
            }
        }

        // 3. Drop `deskripsi` column
        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add `deskripsi` back
        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('image');
        });

        // 2. Migrate data back
        $options = DB::table('opsi_kustom')
            ->whereNotNull('items')
            ->get();

        foreach ($options as $option) {
            $items = json_decode($option->items, true);
            if (is_array($items) && !empty($items)) {
                $deskripsi = implode("\n", array_map(fn($item) => "+ " . $item, $items));
                DB::table('opsi_kustom')
                    ->where('id', $option->id)
                    ->update(['deskripsi' => $deskripsi]);
            }
        }

        // 3. Drop `items`
        Schema::table('opsi_kustom', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }
};
