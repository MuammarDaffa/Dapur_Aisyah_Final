<?php

namespace App\Http\Controllers;

use App\Models\CateringService;
use App\Models\Review;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $services = CateringService::active()->get();

        $reviews = Review::with('user', 'order.cateringService')
            ->latest()
            ->take(6)
            ->get();

        return view('public.landing', compact('services', 'reviews'));
    }
}
