<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_date' => 'required|date|after_or_equal:today',
            'pickup_method' => 'required|in:pickup,delivery',
            'district_id' => 'required_if:pickup_method,delivery|nullable|exists:districts,id',
            'village_id' => 'required_if:pickup_method,delivery|nullable|exists:villages,id',
            'address_detail' => 'required_if:pickup_method,delivery|nullable|string|max:255',
            'serving_type' => 'nullable|string|max:50',
            'portion' => 'nullable|integer|min:1',
            'payment_method' => 'required|in:transfer,cod',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'order_date.required' => 'Tanggal pemesanan wajib diisi.',
            'order_date.after_or_equal' => 'Tanggal pemesanan tidak boleh di masa lalu.',
            'pickup_method.required' => 'Metode pengambilan wajib dipilih.',
            'pickup_method.in' => 'Metode pengambilan harus pickup atau delivery.',
            'district_id.required_if' => 'Kecamatan wajib dipilih untuk pengiriman.',
            'district_id.exists' => 'Kecamatan tidak valid.',
            'village_id.required_if' => 'Kelurahan wajib dipilih untuk pengiriman.',
            'village_id.exists' => 'Kelurahan tidak valid.',
            'address_detail.required_if' => 'Alamat detail wajib diisi untuk pengiriman.',
            'address_detail.max' => 'Alamat detail maksimal 255 karakter.',
            'portion.integer' => 'Jumlah porsi harus berupa angka.',
            'portion.min' => 'Jumlah porsi minimal 1.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran harus transfer atau COD.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
