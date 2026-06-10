<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi saat pesanan dibuat.
     */
    public static function notifyOrderCreated(Order $order): void
    {
        try {
            $order->user->notify(new OrderCreatedNotification($order));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi pesanan dibuat: ' . $e->getMessage(), [
                'order_id' => $order->id,
            ]);
        }
    }

    /**
     * Kirim notifikasi saat pembayaran berhasil.
     */
    public static function notifyPaymentSuccess(Order $order): void
    {
        try {
            $order->user->notify(new PaymentSuccessNotification($order));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi pembayaran berhasil: ' . $e->getMessage(), [
                'order_id' => $order->id,
            ]);
        }
    }

    /**
     * Kirim notifikasi saat status pesanan berubah.
     */
    public static function notifyStatusChanged(Order $order): void
    {
        try {
            $order->user->notify(new OrderStatusChangedNotification($order));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi status berubah: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'status' => $order->status,
            ]);
        }
    }
}
