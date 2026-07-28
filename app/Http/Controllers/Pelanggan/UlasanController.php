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

        Ulasan::create([
            'user_id' => auth()->id(),
            'komentar' => $validated['komentar'],
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
