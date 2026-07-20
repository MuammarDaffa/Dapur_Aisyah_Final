<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tagihan;
use App\Models\Pesanan;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate nomor tagihan unik.
     * Format: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $lastInvoice = Tagihan::where('nomor_tagihan', 'like', "INV-{$date}-%")
            ->orderByDesc('nomor_tagihan')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->nomor_tagihan, -4);
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INV-{$date}-{$newNumber}";
    }

    /**
     * Buat tagihan otomatis saat pesanan dibuat.
     */
    public static function createInvoice(Pesanan $pesanan): Tagihan
    {
        return Tagihan::create([
            'pesanan_id' => $pesanan->id,
            'nomor_tagihan' => self::generateInvoiceNumber(),
            'service_type' => $pesanan->layananKatering->name ?? 'Katering',
            'issued_at' => now(),
        ]);
    }
}
