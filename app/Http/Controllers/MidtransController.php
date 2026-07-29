<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;

class MidtransController extends Controller
{
          public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            
            // 1. Bersihkan akhiran -DP atau -PELUNASAN untuk mencari nomor pesanan asli di database
            $nomorPesananAsli = str_replace(['-DP', '-PELUNASAN'], '', $request->order_id);
            
            // 2. Cari pesanan di database kita
            $pesanan = Pesanan::where('nomor_pesanan', $nomorPesananAsli)->first();

            if ($pesanan) {
                // Jika pembayaran berhasil (settlement atau capture)
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    
                    // CEK APAKAH INI DP ATAU PELUNASAN?
                    if (str_ends_with($request->order_id, '-DP')) {
                        // Jika DP, ubah status_pembayaran jadi DP
                        $pesanan->update(['status_pembayaran' => Pesanan::PEMBAYARAN_DP]);
                    } elseif (str_ends_with($request->order_id, '-PELUNASAN')) {
                        // Jika Pelunasan, ubah status_pembayaran jadi LUNAS
                        $pesanan->update(['status_pembayaran' => Pesanan::PEMBAYARAN_LUNAS]);
                    }
                    
                } 
                // Jika dibatalkan atau kedaluwarsa
                elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'deny' || $request->transaction_status == 'expire') {
                    $pesanan->update([
                        'status_pesanan' => Pesanan::PESANAN_DIBATALKAN,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Callback diterima']);
    }


}
