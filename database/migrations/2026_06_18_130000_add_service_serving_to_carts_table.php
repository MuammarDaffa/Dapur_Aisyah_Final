<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('catering_service_id')->nullable()->after('user_id')
                  ->constrained('catering_services')->nullOnDelete();
            $table->unsignedBigInteger('serving_type_id')->nullable()->after('item_type');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['catering_service_id']);
            $table->dropColumn(['catering_service_id', 'serving_type_id']);
        });
    }
};
