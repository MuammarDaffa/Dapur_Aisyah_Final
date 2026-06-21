<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update data master districts (menambahkan "Kecamatan " di depannya jika belum ada)
        DB::table('districts')
            ->where('name', 'NOT LIKE', 'Kecamatan %')
            ->update([
                'name' => DB::raw("CONCAT('Kecamatan ', name)")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('districts')
            ->where('name', 'LIKE', 'Kecamatan %')
            ->update([
                'name' => DB::raw("REPLACE(name, 'Kecamatan ', '')")
            ]);
    }
};
