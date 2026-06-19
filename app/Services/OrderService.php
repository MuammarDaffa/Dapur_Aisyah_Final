<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\CateringService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Generate nomor pesanan unik.
     * Format: ORD-YYYYMMDD-XXXX
     */
    public static function generateOrderNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $lastOrder = Order::where('order_number', 'like', "ORD-{$date}-%")
            ->orderByDesc('order_number')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ORD-{$date}-{$newNumber}";
    }

    /**
     * Validasi tanggal pemesanan berdasarkan cutoff layanan.
     *
     * Menggunakan minimal_order_days dan cutoff_time dari CateringService.
     * Berlaku untuk semua jenis layanan (Daily, Event, dll).
     *
     * @throws ValidationException
     */
    public static function validateOrderDate(string $orderDate, ?int $cateringServiceId): void
    {
        if (!$cateringServiceId) {
            return;
        }

        $service = CateringService::find($cateringServiceId);
        if (!$service || !$service->minimal_order_days) {
            return;
        }

        $orderDateCarbon = Carbon::parse($orderDate)->startOfDay();
        $now = Carbon::now();
        $today = Carbon::today();

        // Hitung selisih hari
        $daysUntil = (int) $today->diffInDays($orderDateCarbon, false);

        // Cek minimal hari
        if ($daysUntil < $service->minimal_order_days) {
            throw ValidationException::withMessages([
                'order_date' => "Pesanan untuk layanan {$service->name} harus dilakukan minimal {$service->minimal_order_days} hari sebelum tanggal acara.",
            ]);
        }

        // Cek cutoff jam (hanya jika tepat pada batas hari minimal)
        if ($daysUntil == $service->minimal_order_days && $service->cutoff_time) {
            $cutoffTime = substr($service->cutoff_time, 0, 5); // Format H:i
            if ($now->format('H:i') >= $cutoffTime) {
                throw ValidationException::withMessages([
                    'order_date' => "Pesanan untuk layanan {$service->name} harus dilakukan sebelum pukul {$cutoffTime} untuk minimal {$service->minimal_order_days} hari sebelum tanggal acara.",
                ]);
            }
        }
    }

    /**
     * Validasi apakah pesanan dapat dibatalkan oleh pelanggan.
     *
     * @throws ValidationException
     */
    public static function validateCancellation(Order $order): void
    {
        // Pesanan yang sudah dikirim atau selesai tidak bisa dibatalkan
        if (in_array($order->status, ['on_delivery', 'completed', 'cancelled'])) {
            throw ValidationException::withMessages([
                'status' => 'Pesanan dengan status "' . $order->status . '" tidak dapat dibatalkan.',
            ]);
        }

        // Validasi batas waktu pembatalan menggunakan cutoff dari layanan
        if ($order->cateringService && $order->cateringService->minimal_order_days) {
            $orderDate = Carbon::parse($order->order_date);
            $now = Carbon::now();
            $service = $order->cateringService;

            $cancellationDeadline = $orderDate->copy()->subDays($service->minimal_order_days);

            if ($service->cutoff_time) {
                $cutoffParts = explode(':', substr($service->cutoff_time, 0, 5));
                $cancellationDeadline->setTime((int) $cutoffParts[0], (int) $cutoffParts[1], 0);
            } else {
                $cancellationDeadline->startOfDay();
            }

            if ($now->gte($cancellationDeadline)) {
                throw ValidationException::withMessages([
                    'cancellation' => "Pembatalan layanan {$service->name} hanya bisa dilakukan maksimal {$service->minimal_order_days} hari sebelum tanggal acara.",
                ]);
            }
        }
    }

    /**
     * Update status pesanan.
     */
    public static function updateStatus(Order $order, string $newStatus, ?string $cancellationReason = null): Order
    {
        $data = ['status' => $newStatus];

        if ($newStatus === 'completed') {
            $data['payment_status'] = 'paid';
        }

        if ($newStatus === 'cancelled') {
            $data['cancelled_at'] = now();
            $data['cancellation_reason'] = $cancellationReason;
        }

        $order->update($data);

        // Kirim notifikasi
        NotificationService::notifyStatusChanged($order);

        return $order->fresh();
    }
}
