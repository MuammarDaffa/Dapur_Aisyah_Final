<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;

class ReviewController extends Controller
{
    public function index()
    {
        $ulasan = Ulasan::with(['user', 'pesanan.layananKatering'])
            ->latest()
            ->paginate(15);

        return view('admin.ulasan.index', compact('ulasan'));
    }

    public function destroy(Ulasan $ulasan)
    {
        $ulasan->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
