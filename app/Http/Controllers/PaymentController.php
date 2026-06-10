<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        $notification = $request->all();

        Log::info('Midtrans callback received', $notification);

        // Verify signature
        if (!PaymentService::verifySignature($notification)) {
            Log::warning('Midtrans invalid signature', $notification);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_number', $notification['order_id'])->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? null;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
                ]);

                // Kirim notifikasi pembayaran berhasil
                NotificationService::notifyPaymentSuccess($order);
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $order->update([
                'payment_status' => 'failed',
                'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
            ]);
        } elseif ($transactionStatus === 'pending') {
            $order->update([
                'payment_status' => 'unpaid',
                'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
