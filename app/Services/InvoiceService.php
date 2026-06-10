<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate nomor invoice unik.
     * Format: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $lastInvoice = Invoice::where('invoice_number', 'like', "INV-{$date}-%")
            ->orderByDesc('invoice_number')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INV-{$date}-{$newNumber}";
    }

    /**
     * Buat invoice otomatis saat order dibuat.
     */
    public static function createInvoice(Order $order): Invoice
    {
        return Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => self::generateInvoiceNumber(),
            'service_type' => $order->cateringService->name ?? 'Katering',
            'issued_at' => now(),
        ]);
    }
}
