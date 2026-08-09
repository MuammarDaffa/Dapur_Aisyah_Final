<?php

namespace App\Http\Controllers;

// Layanan removed
use App\Models\Ulasan;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $harianBasePrice = \App\Models\Menu::where('tipe_layanan', 'harian')->min('harga') ?? 0;
        
        $acaraBasePrice = \App\Models\TambahanLaukPauk::whereHas('menu', function ($query) {
            $query->where('tipe_layanan', 'acara');
        })->min('harga') ?? 0;

        $services = [
            (object) [
                'tipe_layanan' => 'harian',
                'nama' => 'Katering Harian',
                'base_price' => $harianBasePrice,
            ],
            (object) [
                'tipe_layanan' => 'acara',
                'nama' => 'Katering Acara',
                'base_price' => $acaraBasePrice,
            ]
        ];

        $ulasan = Ulasan::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('public.landing', compact('services', 'ulasan'));
    }
}
