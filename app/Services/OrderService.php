<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pesanan;
use App\Models\LayananKatering;
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
        $lastOrder = Pesanan::where('nomor_pesanan', 'like', "ORD-{$date}-%")
            ->orderByDesc('id')
            ->first();

        if ($lastOrder && preg_match('/ORD-\d{8}-(\d{4})/', $lastOrder->nomor_pesanan, $matches)) {
            $lastNumber = (int) $matches[1];
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ORD-{$date}-{$newNumber}";
    }



    /**
     * Validasi tanggal pemesanan berdasarkan cutoff layanan.
     *
     * Menggunakan minimal_order_days dari LayananKatering.
     * Berlaku untuk semua jenis layanan (Harian, Acara, dll).
     *
     * @throws ValidationException
     */
    public static function validateOrderDate(string $orderDate, ?int $cateringServiceId): void
    {
        if (!$cateringServiceId) {
            return;
        }

        $service = LayananKatering::find($cateringServiceId);
        if (!$service) {
            return;
        }

        // Jika layanan adalah Katering Harian, tidak ada aturan batas jam/hari pemesanan (cutoff).
        // Aturan Katering Harian: pesanan dapat dilakukan selama tanggal menu >= hari ini.
        if ($service->isHarian()) {
            $orderDateCarbon = Carbon::parse($orderDate)->startOfDay();
            $today = Carbon::now('Asia/Jakarta')->startOfDay();
            if ($orderDateCarbon->lt($today)) {
                throw ValidationException::withMessages([
                    'tanggal_pesanan' => "Pesanan katering harian tidak dapat dilakukan untuk tanggal yang sudah lewat.",
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
                'tanggal_pesanan' => "Pesanan untuk layanan {$service->name} harus dilakukan minimal {$minDays} hari sebelum tanggal acara.",
            ]);
        }
    }

    /**
     * Validasi apakah pesanan dapat dibatalkan oleh pelanggan.
     *
     * @throws ValidationException
     */
    public static function validateCancellation(Pesanan $pesanan): void
    {
        // Pesanan yang sudah dikirim atau selesai tidak bisa dibatalkan
        if (in_array($pesanan->status_pesanan, [Pesanan::PESANAN_SELESAI, Pesanan::PESANAN_DIBATALKAN])) {
            throw ValidationException::withMessages([
                'status' => 'Pesanan dengan status pesanan "' . $pesanan->status_pesanan . '" tidak dapat dibatalkan.',
            ]);
        }

        // Validasi batas waktu pembatalan menggunakan cutoff dari layanan (hanya untuk Acara, bukan Harian)
        if ($pesanan->layananKatering) {
            $service = $pesanan->layananKatering;
            
            if (!$service->isHarian() && !is_null($service->minimal_order_days)) {
                $orderDate = Carbon::parse($pesanan->tanggal_pesanan);
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
    public static function updateStatus(Pesanan $pesanan, ?string $newStatusPembayaran = null, ?string $newStatusPesanan = null, ?string $cancellationReason = null): Pesanan
    {
        $data = [];
        if ($newStatusPembayaran) {
            $data['status_pembayaran'] = $newStatusPembayaran;
        }
        if ($newStatusPesanan) {
            $data['status_pesanan'] = $newStatusPesanan;
        }

        // if ($newStatusPesanan === Pesanan::PESANAN_DIBATALKAN) {
        //     $data['dibatalkan_pada'] = now();
        //     $data['alasan_pembatalan'] = $cancellationReason;

        //     // Jika pesanan sudah dilunasi atau ada DP, set refund_status = 'pending'
        //     if (in_array($pesanan->status_pembayaran, [Pesanan::PEMBAYARAN_LUNAS, Pesanan::PEMBAYARAN_DP])) {
        //         $data['refund_status'] = 'pending';
        //     }
        // }
        
        $pesanan->update($data);

        return $pesanan->fresh();
    }
}
