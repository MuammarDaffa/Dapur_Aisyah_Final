<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('catering_service_id')->constrained('catering_services');
            $table->foreignId('package_id')->nullable()->constrained('catering_packages')->nullOnDelete();
            $table->date('order_date'); // Tanggal acara/pengiriman
            $table->enum('pickup_method', ['pickup', 'delivery']);
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->text('address_detail')->nullable();
            $table->string('serving_type', 50)->nullable();
            $table->integer('portion')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('payment_method', ['transfer', 'cod']);
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            $table->string('midtrans_snap_token', 255)->nullable();
            $table->string('midtrans_transaction_id', 255)->nullable();
            $table->enum('status', ['pending_payment', 'processing', 'on_delivery', 'completed', 'cancelled'])->default('pending_payment');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes untuk performa query
            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
