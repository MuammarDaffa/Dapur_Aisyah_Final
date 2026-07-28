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
                $service->base_price = $service->menus()->min('harga') ?? 0;
            } else {
                $service->base_price = \App\Models\MenuItem::whereHas('menu', function ($query) use ($service) {
                    $query->where('layanan_id', $service->id);
                })->min('harga') ?? 0;
            }
            return $service;
        });

        $ulasan = Ulasan::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('public.landing', compact('services', 'ulasan'));
    }
}
