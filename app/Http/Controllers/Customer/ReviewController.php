<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Ulasan;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();

        Ulasan::create([
            'user_id' => auth()->id(),
            'pesanan_id' => $validated['pesanan_id'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
