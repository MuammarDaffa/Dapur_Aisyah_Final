<?php

namespace App\Services;

use App\Models\Order;
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

    public static function createSnapToken(Order $order): string
    {
        self::configureMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => substr($order->user?->name ?? 'Customer', 0, 50),
                'email' => $order->user?->email ?? 'customer@example.com',
                'phone' => substr($order->user?->phone ?? '081234567890', 0, 20),
            ],
            'item_details' => self::getItemDetails($order),
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            // Jika order_id sudah digunakan di Midtrans (misalnya karena collision/testing sandbox setelah reset DB),
            // kita generate unique order_number baru untuk pesanan yang belum dibayar, update DB, lalu coba lagi.
            if ((stripos($msg, 'sudah digunakan') !== false || stripos($msg, 'already been taken') !== false) && $order->payment_status === 'unpaid') {
                \Log::warning("Midtrans Snap Token collision for order {$order->order_number}, generating new unique order_number and retrying...");
                $newOrderNumber = \App\Services\OrderService::generateUniqueOrderNumber($order->order_number);
                $order->update(['order_number' => $newOrderNumber]);
                $params['transaction_details']['order_id'] = $newOrderNumber;
                return Snap::getSnapToken($params);
            }

            // Jika error lain atau masih gagal, log secara rinci lalu lempar exception
            \Log::error("Midtrans Snap Token Error for Order #{$order->order_number}: {$msg}", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'params' => $params,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    private static function getItemDetails(Order $order): array
    {
        $items = [];
        $calculatedTotal = 0;

        foreach ($order->items as $item) {
            $qty = max(1, $item->quantity);
            // Gunakan subtotal / qty agar harga per item akurat termasuk extra/package_item
            $unitPrice = (int) round($item->subtotal / $qty);
            $items[] = [
                'id' => 'item-' . $item->id,
                'price' => $unitPrice,
                'quantity' => $qty,
                'name' => substr($item->item_name, 0, 50),
            ];
            $calculatedTotal += ($unitPrice * $qty);
        }

        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
            $calculatedTotal += (int) $order->shipping_cost;
        }

        // Jika ada selisih antara calculatedTotal dan order->total (karena pembulatan dsb), sesuaikan di item adjustment
        $diff = ((int) $order->total) - $calculatedTotal;
        if ($diff !== 0) {
            $items[] = [
                'id' => 'adjustment',
                'price' => $diff,
                'quantity' => 1,
                'name' => 'Penyesuaian Biaya / Ekstra',
            ];
        }

        return $items;
    }

    public static function verifySignature(array $notification): bool
    {
        self::configureMidtrans();

        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $serverKey = config('midtrans.server_key');

        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return $signature === $notification['signature_key'];
    }

    public static function checkAndSyncStatus(Order $order): void
    {
        if ($order->payment_status === 'paid' || $order->status === 'cancelled') {
            return; // No need to sync if already paid or cancelled
        }

        self::configureMidtrans();

        try {
            $status = \Midtrans\Transaction::status($order->order_number);

            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;

            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                    ]);

                    NotificationService::notifyPaymentSuccess($order);
                }
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $order->update([
                    'payment_status' => 'failed',
                    'midtrans_transaction_id' => $status->transaction_id ?? null,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans sync error: ' . $e->getMessage());
        }
    }
}
