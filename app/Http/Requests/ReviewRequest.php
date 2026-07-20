<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Ulasan;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pesanan_id' => [
                'required',
                'exists:pesanan,id',
                function ($attribute, $value, $fail) {
                    // Cek apakah pesanan milik user
                    $pesanan = \App\Models\Pesanan::find($value);
                    if ($pesanan && $pesanan->user_id !== auth()->id()) {
                        $fail('Pesanan ini bukan milik Anda.');
                    }

                    // Cek apakah pesanan sudah selesai
                    if ($pesanan && $pesanan->status !== 'selesai') {
                        $fail('Ulasan hanya dapat diberikan untuk pesanan yang sudah selesai.');
                    }

                    // Cek apakah sudah ada ulasan
                    if (Ulasan::where('pesanan_id', $value)->exists()) {
                        $fail('Anda sudah memberikan ulasan untuk pesanan ini.');
                    }
                },
            ],
            'comment' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'pesanan_id.required' => 'ID pesanan wajib diisi.',
            'pesanan_id.exists' => 'Pesanan tidak ditemukan.',
            'comment.required' => 'Komentar ulasan wajib diisi.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ];
    }
}
