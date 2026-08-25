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
            
            // 1. Ekstrak nomor pesanan asli dan jenis pembayaran
            // Format order_id: ORD-20260729-0001-DP-1738201231
            $parts = explode('-', $request->order_id);
            if (count($parts) >= 4) {
                $nomorPesananAsli = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
                $jenisPembayaran = $parts[3]; // DP atau PELUNASAN
            } else {
                return response()->json(['message' => 'Format order_id tidak valid'], 400);
            }
            
            // 2. Cari pesanan di database kita
            $pesanan = Pesanan::where('nomor_pesanan', $nomorPesananAsli)->first();

            if ($pesanan) {
                // Jika pembayaran berhasil (settlement atau capture)
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    
                    // CEK APAKAH INI DP ATAU PELUNASAN?
                    if ($jenisPembayaran === 'DP') {
                        // Jika DP, ubah status_pembayaran jadi DP
                        $pesanan->status_pembayaran = Pesanan::PEMBAYARAN_DP;
                    } elseif ($jenisPembayaran === 'PELUNASAN') {
                        // Jika Pelunasan, ubah status_pembayaran jadi LUNAS
                        $pesanan->status_pembayaran = Pesanan::PEMBAYARAN_LUNAS;
                        if (is_null($pesanan->kode_pengambilan)) {
                            $pesanan->kode_pengambilan = Pesanan::generateKodePengambilan();
                        }
                    }

                    // Jika status_pesanan masih null, ubah menjadi diproses
                    if (is_null($pesanan->status_pesanan)) {
                        $pesanan->status_pesanan = Pesanan::PESANAN_DIPROSES;
                        
                        // KURANGI STOK KETIKA LUNAS (Hanya untuk pesanan katering harian)
                        if ($pesanan->tipe_layanan === 'harian') {
                            foreach ($pesanan->detailPesanans as $detail) {
                                $jadwal = \App\Models\JadwalMenu::where('menu_id', $detail->menu_id)
                                    ->whereDate('tanggal', $detail->tanggal_pengiriman)
                                    ->first();
                                if ($jadwal) {
                                    $jadwal->stok_tersisa -= $detail->porsi;
                                    $jadwal->save();
                                }
                            }
                        }
                    }

                    $pesanan->save();
                    
                } 
                // Jika dibatalkan atau kedaluwarsa
                elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'deny' || $request->transaction_status == 'expire') {
                    if ($pesanan->status_pesanan !== Pesanan::PESANAN_DIBATALKAN) {
                        $pesanan->update([
                            'status_pesanan' => Pesanan::PESANAN_DIBATALKAN,
                        ]);


                    }
                }
            }
        }

        return response()->json(['message' => 'Callback diterima']);
    }


}
