<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pesanan;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi saat pesanan dibuat.
     */
    public static function notifyOrderCreated(Pesanan $pesanan): void
    {
        try {
            $pesanan->user->notify(new OrderCreatedNotification($pesanan));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi pesanan dibuat: ' . $e->getMessage(), [
                'pesanan_id' => $pesanan->id,
            ]);
        }
    }

    /**
     * Kirim notifikasi saat pembayaran berhasil.
     */
    public static function notifyPaymentSuccess(Pesanan $pesanan): void
    {
        try {
            $pesanan->user->notify(new PaymentSuccessNotification($pesanan));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi pembayaran berhasil: ' . $e->getMessage(), [
                'pesanan_id' => $pesanan->id,
            ]);
        }
    }

    /**
     * Kirim notifikasi saat status pesanan berubah.
     */
    public static function notifyStatusChanged(Pesanan $pesanan): void
    {
        try {
            $pesanan->user->notify(new OrderStatusChangedNotification($pesanan));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi status berubah: ' . $e->getMessage(), [
                'pesanan_id' => $pesanan->id,
                'status' => $pesanan->status,
            ]);
        }
    }
}
