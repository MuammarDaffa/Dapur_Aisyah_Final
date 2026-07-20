<?php

namespace App\Http\Controllers;

use App\Models\LayananKatering;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $services = LayananKatering::active()->get();

        $ulasan = Ulasan::with('user', 'pesanan.layananKatering')
            ->latest()
            ->take(6)
            ->get();

        return view('public.landing', compact('services', 'ulasan'));
    }
}
