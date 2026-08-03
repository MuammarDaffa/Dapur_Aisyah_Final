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
        // 1. Add tipe_layanan to pesanan, menu, minumans
        if (!Schema::hasColumn('pesanan', 'tipe_layanan')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->enum('tipe_layanan', ['harian', 'acara'])->default('harian')->after('user_id');
            });
        }

        if (!Schema::hasColumn('menu', 'tipe_layanan')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->enum('tipe_layanan', ['harian', 'acara'])->default('harian')->after('id');
            });
        }

        if (!Schema::hasColumn('minumans', 'tipe_layanan')) {
            Schema::table('minumans', function (Blueprint $table) {
                $table->enum('tipe_layanan', ['harian', 'acara'])->default('acara')->after('id');
            });
        }

        // 2. Migrate data (only if layanan table still exists)
        if (Schema::hasTable('layanan')) {
            $layanans = DB::table('layanan')->get();
            foreach ($layanans as $layanan) {
                if (Schema::hasColumn('pesanan', 'layanan_id')) {
                    DB::table('pesanan')->where('layanan_id', $layanan->id)->update(['tipe_layanan' => $layanan->tipe]);
                }
                if (Schema::hasColumn('menu', 'layanan_id')) {
                    DB::table('menu')->where('layanan_id', $layanan->id)->update(['tipe_layanan' => $layanan->tipe]);
                }
                if (Schema::hasColumn('minumans', 'layanan_id')) {
                    DB::table('minumans')->where('layanan_id', $layanan->id)->update(['tipe_layanan' => $layanan->tipe]);
                }
            }
        }

        // 3. Drop foreign keys and columns
        if (Schema::hasColumn('pesanan', 'layanan_id')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->dropForeign(['layanan_id']);
                $table->dropColumn('layanan_id');
            });
        }

        if (Schema::hasColumn('menu', 'layanan_id')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->dropForeign('menu_harian_layanan_id_foreign');
                $table->dropColumn('layanan_id');
            });
        }

        if (Schema::hasColumn('minumans', 'layanan_id')) {
            Schema::table('minumans', function (Blueprint $table) {
                $table->dropForeign('minumans_layanan_id_foreign');
                $table->dropColumn('layanan_id');
            });
        }

        // 4. Drop layanan table
        Schema::dropIfExists('layanan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['harian', 'acara']);
            $table->boolean('status')->default(true);
            $table->integer('kapasitas_porsi_per_minggu')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Reverse for pesanan
        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreignId('layanan_id')->nullable()->constrained('layanan')->cascadeOnDelete();
        });
        
        // Reverse for menu
        Schema::table('menu', function (Blueprint $table) {
            $table->foreignId('layanan_id')->nullable()->constrained('layanan')->cascadeOnDelete();
        });

        // Reverse for minumans
        Schema::table('minumans', function (Blueprint $table) {
            $table->foreignId('layanan_id')->nullable()->constrained('layanan')->cascadeOnDelete();
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('tipe_layanan');
        });

        Schema::table('menu', function (Blueprint $table) {
            $table->dropColumn('tipe_layanan');
        });

        Schema::table('minumans', function (Blueprint $table) {
            $table->dropColumn('tipe_layanan');
        });
    }
};
