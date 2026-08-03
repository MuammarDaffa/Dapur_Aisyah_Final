<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function edit()
    {
        return view('pelanggan.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|08)[0-9]{8,13}$/'],
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function riwayatPesanan()
    {
        $riwayatPesanan = \App\Models\Pesanan::where('user_id', auth()->id())->latest()->get();
        return view('pelanggan.riwayat_pesanan', compact('riwayatPesanan'));
    }
    public function hapusPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::with('detailPesanans')->findOrFail($id);

        if ($pesanan->user_id !== auth()->id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($pesanan->status_pembayaran !== \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR) {
            return back()->with('error', 'Hanya pesanan yang belum dibayar yang dapat dihapus.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            foreach ($pesanan->detailPesanans as $detail) {
                $detail->menuItems()->detach();
            }
            $pesanan->detailPesanans()->delete();
            $pesanan->delete();

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus pesanan.');
        }
    }
}
