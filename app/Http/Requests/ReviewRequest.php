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
            'pesanan_id' => 'required|exists:pesanan,id',
            'komentar' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'pesanan_id.required' => 'Pesanan tidak valid.',
            'pesanan_id.exists' => 'Pesanan tidak ditemukan.',
            'komentar.required' => 'Komentar ulasan wajib diisi.',
            'komentar.max' => 'Komentar maksimal 1000 karakter.',
        ];
    }
}
