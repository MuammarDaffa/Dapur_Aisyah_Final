<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Ulasan;

class UlasanController extends Controller
{
    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();

        if (Ulasan::where('pesanan_id', $validated['pesanan_id'])->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah memberikan penilaian untuk pesanan ini.']);
            }
            return back()->with('error', 'Anda sudah memberikan penilaian untuk pesanan ini.');
        }

        Ulasan::create([
            'user_id' => auth()->id(),
            'pesanan_id' => $validated['pesanan_id'],
            'komentar' => $validated['komentar'],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Penilaian berhasil dikirim. Terima kasih!']);
        }

        return back()->with('success', 'Penilaian berhasil dikirim. Terima kasih!');
    }
}
