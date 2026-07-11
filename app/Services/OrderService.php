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
            ->orderByDesc('id')
            ->first();

        if ($lastOrder && preg_match('/ORD-\d{8}-(\d{4})/', $lastOrder->order_number, $matches)) {
            $lastNumber = (int) $matches[1];
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ORD-{$date}-{$newNumber}";
    }

    /**
     * Generate nomor pesanan unik khusus saat terjadi collision di Midtrans (misalnya sehabis reset DB).
     */
    public static function generateUniqueOrderNumber(?string $oldOrderNumber = null): string
    {
        if ($oldOrderNumber && !str_contains($oldOrderNumber, '-R')) {
            return $oldOrderNumber . '-R' . rand(100, 999);
        }
        return self::generateOrderNumber() . '-R' . rand(100, 999);
    }

    /**
     * Validasi tanggal pemesanan berdasarkan cutoff layanan.
     *
     * Menggunakan minimal_order_days dari CateringService.
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
        if (!$service) {
            return;
        }

        // Jika layanan adalah Katering Harian (Daily), tidak ada aturan batas jam/hari pemesanan (cutoff).
        // Aturan Katering Harian: pesanan dapat dilakukan selama tanggal menu >= hari ini.
        if ($service->isDaily()) {
            $orderDateCarbon = Carbon::parse($orderDate)->startOfDay();
            $today = Carbon::now('Asia/Jakarta')->startOfDay();
            if ($orderDateCarbon->lt($today)) {
                throw ValidationException::withMessages([
                    'order_date' => "Pesanan katering harian tidak dapat dilakukan untuk tanggal yang sudah lewat.",
                ]);
            }
            return;
        }

        // Jika minimal_order_days kosong, berarti tidak ada aturan cutoff sama sekali
        if (is_null($service->minimal_order_days)) {
            return;
        }

        $orderDateCarbon = Carbon::parse($orderDate)->startOfDay();
        $today = Carbon::today();

        // Hitung selisih hari
        $daysUntil = (int) $today->diffInDays($orderDateCarbon, false);
        $minDays = $service->minimal_order_days ?? 0;

        // Cek minimal hari
        if ($daysUntil < $minDays) {
            throw ValidationException::withMessages([
                'order_date' => "Pesanan untuk layanan {$service->name} harus dilakukan minimal {$minDays} hari sebelum tanggal acara.",
            ]);
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

        // Validasi batas waktu pembatalan menggunakan cutoff dari layanan (hanya untuk Event, bukan Daily)
        if ($order->cateringService) {
            $service = $order->cateringService;
            
            if (!$service->isDaily() && !is_null($service->minimal_order_days)) {
                $orderDate = Carbon::parse($order->order_date);
                $now = Carbon::now();
                $minDays = $service->minimal_order_days ?? 0;

                $cancellationDeadline = $orderDate->copy()->subDays($minDays)->startOfDay();

                if ($now->gte($cancellationDeadline)) {
                    throw ValidationException::withMessages([
                        'cancellation' => "Pembatalan layanan {$service->name} hanya bisa dilakukan sebelum tenggat waktu ({$minDays} hari sebelumnya).",
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

            // Jika pesanan sudah dibayar, set refund_status = 'pending'
            if ($order->payment_status === 'paid') {
                $data['refund_status'] = 'pending';
            }
        }

        $order->update($data);

        // Kirim notifikasi
        NotificationService::notifyStatusChanged($order);

        return $order->fresh();
    }
}
