<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Review;
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
            'order_id' => [
                'required',
                'exists:orders,id',
                function ($attribute, $value, $fail) {
                    // Cek apakah order milik user
                    $order = \App\Models\Order::find($value);
                    if ($order && $order->user_id !== auth()->id()) {
                        $fail('Pesanan ini bukan milik Anda.');
                    }

                    // Cek apakah order sudah selesai
                    if ($order && $order->status !== 'completed') {
                        $fail('Ulasan hanya dapat diberikan untuk pesanan yang sudah selesai.');
                    }

                    // Cek apakah sudah ada ulasan
                    if (Review::where('order_id', $value)->exists()) {
                        $fail('Anda sudah memberikan ulasan untuk pesanan ini.');
                    }
                },
            ],
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'ID pesanan wajib diisi.',
            'order_id.exists' => 'Pesanan tidak ditemukan.',
            'rating.required' => 'Rating wajib diisi.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.between' => 'Rating harus antara 1 sampai 5.',
            'comment.max' => 'Komentar maksimal 500 karakter.',
        ];
    }
}
