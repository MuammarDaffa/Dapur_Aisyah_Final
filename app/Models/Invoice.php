<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Invoice menyimpan data tagihan pembayaran untuk sebuah pesanan.
 * Terhubung (relasi) dengan entitas Order dan menyimpan bukti PDF jika ada.
 */
class Invoice extends Model
{
    protected $fillable = [
        'order_id', 'invoice_number', 'service_type', 'issued_at', 'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    /**
         * Menghasilkan nomor invoice unik secara otomatis.
         * Format yang dihasilkan: INV-YYYYMMDD-XXXX (contoh: INV-20260718-0001).
         * @return string
         */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $lastInvoice = static::where('invoice_number', 'like', "INV-{$date}-%")
                            ->orderBy('invoice_number', 'desc')
                            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "INV-{$date}-{$nextNumber}";
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
