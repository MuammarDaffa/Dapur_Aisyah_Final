<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('custom_option_product');
        Schema::dropIfExists('item_periode_menu');
        Schema::dropIfExists('periode_menu');
        Schema::dropIfExists('produk');
    }

    public function down(): void
    {
        // One way migration
    }
};