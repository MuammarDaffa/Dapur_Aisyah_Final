<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model OrderItem merepresentasikan satuan item/menu dalam sebuah pesanan.
 * Menyimpan nama final item (termasuk kustomisasi) serta harga dan subtotal per item.
 */
class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'custom_option_id',
        'item_name', 'quantity', 'unit_price', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customOption(): BelongsTo
    {
        return $this->belongsTo(CustomOption::class);
    }

    /**
     * Nama menu yang sudah dibersihkan dari tag Extra dan Kirim.
     */
    /**
         * Mendapatkan nama menu inti dengan menghapus teks atribut tambahan (seperti Ekstra atau Kirim).
         * Tujuannya agar rekapitulasi nama makanan di dapur menjadi lebih bersih.
         * @return string
         */
    public function getFormattedMenuNameAttribute(): string
    {
        $name = $this->item_name ?? '';

        // Hapus tag [Kirim: ...] terlebih dahulu
        $name = preg_replace('/\s*\[Kirim:.*?\]/i', '', $name);
        // Hapus bagian (Extra: ...) atau Extra: ... yang ada di akhir string
        $name = preg_replace('/\s*\(\s*Extra:.*$/i', '', $name);
        $name = preg_replace('/\s*Extra:.*$/i', '', $name);
        // Hapus prefix "Menu: " dsb apabila ada
        $name = preg_replace('/^(Menu|Paket|Penyajian):\s*/i', '', $name);

        return trim($name);
    }

    /**
     * Daftar extra yang diformat ringkas, misal "Sayur Sup (1), Sambal (1)" atau null jika tidak ada.
     */
    /**
         * Mengekstrak detail opsi tambahan (ekstra) dari dalam string nama item dan memformatnya kembali.
         * Proses: Membaca pola (Extra: ...) menggunakan Regex dan memisahkannya.
         * Output yang diharapkan: "Sayur Sup (1), Sambal (2)".
         * @return string|null
         */
    public function getFormattedExtrasAttribute(): ?string
    {
        $raw = $this->item_name ?? '';

        // Jika item ini sendiri merupakan baris Extra pada event catering (misal "Extra: Es Buah"), maka tidak memiliki sub-extra
        if (preg_match('/^Extra:\s*/i', trim($raw))) {
            return null;
        }

        // Hapus tag [Kirim: ...] terlebih dahulu
        $clean = preg_replace('/\s*\[Kirim:.*?\]/i', '', $raw);

        // Ekstrak string ekstra
        if (preg_match('/\(\s*Extra:\s*(.+?)\s*\)\s*$/i', trim($clean), $matches)) {
            $rawExtras = $matches[1];
        } elseif (preg_match('/\(\s*Extra:\s*(.+?)\)/i', $clean, $matches)) {
            $rawExtras = $matches[1];
        } elseif (preg_match('/\s+Extra:\s*(.+?)$/i', trim($clean), $matches)) {
            $rawExtras = $matches[1];
        } else {
            return null;
        }

        $rawExtras = trim($rawExtras);
        if (empty($rawExtras)) {
            return null;
        }

        // Pecah berdasarkan koma
        $parts = explode(',', $rawExtras);
        $formatted = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            // Hapus informasi harga seperti (+Rp10.000), (+Rp 2.000), atau (Rp10.000)
            $part = preg_replace('/\s*\(\+?Rp.*?\)/i', '', $part);
            $part = trim($part);

            // Ubah format jumlah, misal "Sayur Sup 1x" -> "Sayur Sup (1)"
            if (preg_match('/^(.+?)\s+(\d+)x$/i', $part, $m)) {
                $formatted[] = trim($m[1]) . ' (' . $m[2] . ')';
            } elseif (preg_match('/^(.+?)\s*\(\s*(\d+)\s*\)$/', $part, $m)) {
                // Jika sudah format "Sayur Sup (1)"
                $formatted[] = trim($m[1]) . ' (' . $m[2] . ')';
            } else {
                // Jika tanpa indikator jumlah (misal hanya "Sayur Sup"), asumsikan (1)
                $formatted[] = $part . ' (1)';
            }
        }

        return !empty($formatted) ? implode(', ', $formatted) : null;
    }
}
