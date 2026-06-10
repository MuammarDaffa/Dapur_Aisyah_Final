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
     * Validasi tanggal pemesanan berdasarkan jenis layanan.
     *
     * - Katering Harian: H-1 sebelum jam 21:00 WIB
     * - Katering Acara Kantoran: H-3 sebelum tanggal acara
     *
     * @throws ValidationException
     */
    public static function validateOrderDate(string $orderDate, ?int $cateringServiceId): void
    {
        if (!$cateringServiceId) {
            return;
        }

        $service = CateringService::find($cateringServiceId);
        if (!$service) {
            return;
        }

        $orderDateCarbon = Carbon::parse($orderDate);
        $now = Carbon::now();

        // Cek apakah ini katering harian menggunakan fitur
        $isDaily = $service->isDaily();
        // Cek apakah ini katering acara menggunakan fitur
        $isEvent = $service->isEvent();

        if ($isDaily) {
            // Pemesanan harian: H-1 sebelum jam 21:00
            $deadline = $now->copy()->setTime(21, 0, 0);
            $minDate = $now->isBefore($deadline)
                ? Carbon::tomorrow()
                : Carbon::now()->addDays(2);

            if ($orderDateCarbon->lt($minDate)) {
                throw ValidationException::withMessages([
                    'order_date' => 'Pemesanan katering harian minimal H-1 sebelum jam 21:00 WIB.',
                ]);
            }
        } elseif ($isEvent) {
            // Pemesanan acara: H-3 sebelum tanggal acara
            $minDate = $now->copy()->addDays(3)->startOfDay();

            if ($orderDateCarbon->lt($minDate)) {
                throw ValidationException::withMessages([
                    'order_date' => 'Pemesanan katering acara kantoran/event minimal H-3 sebelum tanggal acara.',
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

        // Validasi batas waktu pembatalan
        $orderDate = Carbon::parse($order->order_date);
        $now = Carbon::now();

        if ($order->cateringService) {
            $isDaily = $order->cateringService->isDaily();
            $isEvent = $order->cateringService->isEvent();

            if ($isDaily) {
                // H-1 sebelum jam 21:00
                $cancellationDeadline = $orderDate->copy()->subDay()->setTime(21, 0, 0);
                if ($now->gte($cancellationDeadline)) {
                    throw ValidationException::withMessages([
                        'cancellation' => 'Pembatalan katering harian hanya bisa dilakukan maksimal H-1 sebelum jam 21:00.',
                    ]);
                }
            } elseif ($isEvent) {
                // H-3 sebelum acara
                $cancellationDeadline = $orderDate->copy()->subDays(3)->startOfDay();
                if ($now->gte($cancellationDeadline)) {
                    throw ValidationException::withMessages([
                        'cancellation' => 'Pembatalan katering acara kantoran/event hanya bisa dilakukan maksimal H-3 sebelum acara.',
                    ]);
                }
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
