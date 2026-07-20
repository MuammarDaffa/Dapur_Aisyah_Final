<?php

namespace App\Services;

use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentService
{
    public static function configureMidtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public static function createSnapToken(Pesanan $pesanan): string
    {
        self::configureMidtrans();

        $params = [
            'transaction_details' => [
                'pesanan_id' => $pesanan->nomor_pesanan,
                'gross_amount' => (int) $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => substr($pesanan->user?->name ?? 'Pelanggan', 0, 50),
                'email' => $pesanan->user?->email ?? 'customer@example.com',
                'phone' => substr($pesanan->user?->phone ?? '081234567890', 0, 20),
            ],
            'item_details' => self::getItemDetails($pesanan),
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            // Jika pesanan_id sudah digunakan di Midtrans (misalnya karena collision/testing sandbox setelah reset DB),
            // kita generate unique nomor_pesanan baru untuk pesanan yang belum dibayar, update DB, lalu coba lagi.
            if ((stripos($msg, 'sudah digunakan') !== false || stripos($msg, 'already been taken') !== false) && $pesanan->status_pembayaran === 'belum_dibayar') {
                \Log::warning("Midtrans Snap Token collision for pesanan {$pesanan->nomor_pesanan}, generating new unique nomor_pesanan and retrying...");
                $newOrderNumber = \App\Services\OrderService::generateUniqueOrderNumber($pesanan->nomor_pesanan);
                $pesanan->update(['nomor_pesanan' => $newOrderNumber]);
                $params['transaction_details']['pesanan_id'] = $newOrderNumber;
                return Snap::getSnapToken($params);
            }

            // Jika error lain atau masih gagal, log secara rinci lalu lempar exception
            \Log::error("Midtrans Snap Token Error for Pesanan #{$pesanan->nomor_pesanan}: {$msg}", [
                'pesanan_id' => $pesanan->id,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'params' => $params,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    private static function getItemDetails(Pesanan $pesanan): array
    {
        $items = [];
        $calculatedTotal = 0;

        foreach ($pesanan->items as $item) {
            $qty = max(1, $item->jumlah);
            // Gunakan subtotal / qty agar harga per item akurat termasuk extra/package_item
            $unitPrice = (int) round($item->subtotal / $qty);
            $items[] = [
                'id' => 'item-' . $item->id,
                'harga' => $unitPrice,
                'jumlah' => $qty,
                'name' => substr($item->item_name, 0, 50),
            ];
            $calculatedTotal += ($unitPrice * $qty);
        }

        if ($pesanan->ongkos_kirim > 0) {
            $items[] = [
                'id' => 'shipping',
                'harga' => (int) $pesanan->ongkos_kirim,
                'jumlah' => 1,
                'name' => 'Ongkos Kirim',
            ];
            $calculatedTotal += (int) $pesanan->ongkos_kirim;
        }

        // Jika ada selisih antara calculatedTotal dan pesanan->total (karena pembulatan dsb), sesuaikan di item adjustment
        $diff = ((int) $pesanan->total) - $calculatedTotal;
        if ($diff !== 0) {
            $items[] = [
                'id' => 'adjustment',
                'harga' => $diff,
                'jumlah' => 1,
                'name' => 'Penyesuaian Biaya / Ekstra',
            ];
        }

        return $items;
    }

    public static function verifySignature(array $notification): bool
    {
        self::configureMidtrans();

        $orderId = $notification['pesanan_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $serverKey = config('midtrans.server_key');

        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return $signature === $notification['signature_key'];
    }

    public static function checkAndSyncStatus(Pesanan $pesanan): void
    {
        if ($pesanan->status_pembayaran === 'sudah_dibayar' || $pesanan->status === 'dibatalkan') {
            return; // No need to sync if already paid or cancelled
        }

        self::configureMidtrans();

        try {
            $status = \Midtrans\Transaction::status($pesanan->nomor_pesanan);

            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;

            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $pesanan->update([
                        'status_pembayaran' => 'sudah_dibayar',
                        'status' => 'diproses',
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                    ]);

                    NotificationService::notifyPaymentSuccess($pesanan);
                }
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $pesanan->update([
                    'status_pembayaran' => 'gagal',
                    'midtrans_transaction_id' => $status->transaction_id ?? null,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans sync error: ' . $e->getMessage());
        }
    }
}
