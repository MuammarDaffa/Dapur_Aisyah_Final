<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
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

        $pesanan = Pesanan::where('nomor_pesanan', $notification['pesanan_id'])->first();

        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan not found'], 404);
        }

        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? null;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                $pesanan->update([
                    'status_pembayaran' => 'sudah_dibayar',
                    'status' => 'diproses',
                    'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
                ]);

                // Kirim notifikasi pembayaran berhasil
                NotificationService::notifyPaymentSuccess($pesanan);
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $pesanan->update([
                'status_pembayaran' => 'gagal',
                'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
            ]);
        } elseif ($transactionStatus === 'pending') {
            $pesanan->update([
                'status_pembayaran' => 'belum_dibayar',
                'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
