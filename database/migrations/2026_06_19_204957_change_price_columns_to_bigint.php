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
        Schema::table('catering_services', function (Blueprint $table) {
            $table->decimal('base_price', 15, 2)->default(0)->change();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
        });
        Schema::table('catering_packages', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
        });
        Schema::table('custom_options', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->default(0)->change();
        });
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->decimal('cost', 15, 2)->default(20000)->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->default(0)->change();
            $table->decimal('shipping_cost', 15, 2)->default(0)->change();
            $table->decimal('subtotal', 15, 2)->default(0)->change();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('subtotal', 15, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('catering_services', function (Blueprint $table) {
            $table->decimal('base_price', 12, 2)->default(0)->change();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->change();
        });
        Schema::table('catering_packages', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->change();
        });
        Schema::table('custom_options', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->change();
        });
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->default(20000)->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total', 12, 2)->default(0)->change();
            $table->decimal('shipping_cost', 10, 2)->default(0)->change();
            $table->decimal('subtotal', 12, 2)->default(0)->change();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('subtotal', 12, 2)->change();
        });
    }
};
