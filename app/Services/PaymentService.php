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
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            'item_details' => self::getItemDetails($order),
        ];

        return Snap::getSnapToken($params);
    }

    private static function getItemDetails(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => 'item-' . $item->id,
                'price' => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name' => substr($item->item_name, 0, 50),
            ];
        }




        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
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
