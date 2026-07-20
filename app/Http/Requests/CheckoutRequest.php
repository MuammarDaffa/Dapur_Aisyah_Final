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
            'tanggal_pesanan' => 'nullable|date',
            'metode_pengambilan' => 'required|in:pickup,delivery',
            'kecamatan_id' => 'required_if:metode_pengambilan,delivery|nullable|exists:kecamatan,id',
            'desa_id' => 'nullable|exists:desa,id',
            'detail_alamat' => 'nullable|string|max:255',
            'osm_address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tipe_penyajian' => 'nullable|string|max:50',
            'porsi' => 'nullable|integer|min:1',
            'metode_pembayaran' => 'required|in:transfer',
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('metode_pengambilan') === 'delivery') {
                $validation = \App\Services\LocationService::validateLocation(
                    $this->input('latitude'),
                    $this->input('longitude'),
                    $this->input('kecamatan_id'),
                    null,
                    $this->input('osm_address')
                );

                if (!$validation['is_in_pontianak']) {
                    $validator->errors()->add('kecamatan_id', $validation['message'] ?? 'Lokasi berada di luar wilayah Pontianak.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'tanggal_pesanan.required' => 'Tanggal pemesanan wajib diisi.',
            'tanggal_pesanan.after_or_equal' => 'Tanggal pemesanan tidak boleh di masa lalu.',
            'metode_pengambilan.required' => 'Metode pengambilan wajib dipilih.',
            'metode_pengambilan.in' => 'Metode pengambilan harus pickup atau delivery.',
            'kecamatan_id.required_if' => 'Kecamatan wajib dipilih untuk pengiriman.',
            'kecamatan_id.exists' => 'Kecamatan tidak valid.',
            'desa_id.exists' => 'Kelurahan tidak valid.',
            'detail_alamat.max' => 'Alamat detail maksimal 255 karakter.',
            'porsi.integer' => 'Jumlah porsi harus berupa angka.',
            'porsi.min' => 'Jumlah porsi minimal 1.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran harus transfer (Midtrans).',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}

