<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $services = Layanan::where('status', true)->get()->map(function($service) {
            if ($service->isHarian()) {
                $service->base_price = $service->menuHarian()->min('harga') ?? 0;
            } else {
                $service->base_price = \App\Models\IsiMenu::whereHas('menuAcara', function ($query) use ($service) {
                    $query->where('layanan_id', $service->id);
                })->min('harga') ?? 0;
            }
            return $service;
        });

        $ulasan = Ulasan::with('user', 'pesanan.layanan')
            ->latest()
            ->take(6)
            ->get();

        return view('public.landing', compact('services', 'ulasan'));
    }
}
