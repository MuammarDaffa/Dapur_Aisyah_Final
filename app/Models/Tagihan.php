<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Tagihan menyimpan data tagihan pembayaran untuk sebuah pesanan.
 * Terhubung (relasi) dengan entitas Pesanan dan menyimpan bukti PDF jika ada.
 */
class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'pesanan_id', 'nomor_tagihan', 'service_type', 'issued_at', 'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    /**
         * Menghasilkan nomor tagihan unik secara otomatis.
         * Format yang dihasilkan: INV-YYYYMMDD-XXXX (contoh: INV-20260718-0001).
         * @return string
         */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $lastInvoice = static::where('nomor_tagihan', 'like', "INV-{$date}-%")
                            ->orderBy('nomor_tagihan', 'desc')
                            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->nomor_tagihan, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "INV-{$date}-{$nextNumber}";
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}
