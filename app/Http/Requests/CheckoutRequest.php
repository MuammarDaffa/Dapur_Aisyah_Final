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
            'order_date' => 'nullable|date',
            'pickup_method' => 'required|in:pickup,delivery',
            'district_id' => 'required_if:pickup_method,delivery|nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'address_detail' => 'nullable|string|max:255',
            'osm_address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'serving_type' => 'nullable|string|max:50',
            'portion' => 'nullable|integer|min:1',
            'payment_method' => 'required|in:transfer',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('pickup_method') === 'delivery') {
                $validation = \App\Services\LocationService::validateLocation(
                    $this->input('latitude'),
                    $this->input('longitude'),
                    $this->input('district_id'),
                    null,
                    $this->input('osm_address')
                );

                if (!$validation['is_in_pontianak']) {
                    $validator->errors()->add('district_id', $validation['message'] ?? 'Lokasi berada di luar wilayah Pontianak.');
                }
            }
        });
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
            'village_id.exists' => 'Kelurahan tidak valid.',
            'address_detail.max' => 'Alamat detail maksimal 255 karakter.',
            'portion.integer' => 'Jumlah porsi harus berupa angka.',
            'portion.min' => 'Jumlah porsi minimal 1.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran harus transfer (Midtrans).',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}

