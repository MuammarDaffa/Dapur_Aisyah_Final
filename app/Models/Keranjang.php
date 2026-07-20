<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Keranjang merepresentasikan item keranjang belanja pelanggan sebelum checkout.
 * Bertanggung jawab menyimpan data produk, paket, atau kustomisasi yang dipilih beserta kuantitasnya.
 */
class Keranjang extends Model
{
    protected $table = 'keranjang';

    protected $fillable = [
        'user_id', 'layanan_katering_id', 'cart_group_id', 'produk_id',
        'opsi_kustom_id', 'catering_package_id', 'extras',
        'jumlah', 'item_type', 'serving_type_id', 'menu_date', 'catatan',
    ];

    protected $casts = [
        'extras' => 'array',
        'menu_date' => 'date',
    ];

    // === Relationships ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function opsiKustom(): BelongsTo
    {
        return $this->belongsTo(OpsiKustom::class);
    }

    public function paketKatering(): BelongsTo
    {
        return $this->belongsTo(PaketKatering::class);
    }

    public function layananKatering(): BelongsTo
    {
        return $this->belongsTo(LayananKatering::class);
    }

    public function servingType(): BelongsTo
    {
        return $this->belongsTo(OpsiKustom::class, 'serving_type_id');
    }

    // === Helpers ===

    /**
     * Cek apakah item ini adalah bagian dari event pesanan.
     */
    /**
         * Mengecek apakah item keranjang ini merupakan pesanan Event (Prasmanan/Kotakan).
         * Ditandai dengan adanya cart_group_id.
         * @return bool
         */
    public function isAcaraItem(): bool
    {
        return !empty($this->cart_group_id);
    }

    /**
     * Cek apakah item ini adalah harian (bukan event).
     */
    /**
         * Mengecek apakah item keranjang ini merupakan pesanan Katering Harian.
         * Ditandai dengan cart_group_id yang kosong (null).
         * @return bool
         */
    public function isHarianItem(): bool
    {
        return empty($this->cart_group_id);
    }

    /**
     * Cek apakah item ini sudah termasuk dalam paket (harga = 0).
     */
    public function isBundledInPackage(): bool
    {
        return $this->item_type === 'package_item';
    }

    /**
     * Menghitung subtotal item keranjang.
     * Item yang termasuk dalam paket (package_item) = 0.
     * Item tambahan dihitung normal.
     * Item tipe 'package' = harga paket itu sendiri.
     */
    /**
         * Menghitung subtotal dari satu item keranjang.
         * Jika item adalah bagian dari paket (package_item), harga dianggap 0.
         * Jika berupa produk harian/tambahan, dihitung (harga dasar x qty) + harga opsi tambahan.
         * @return float
         */
    public function getSubtotalAttribute(): float
    {
        // Item paket (isi paket atau extra paket) atau custom_header → harga 0, sudah termasuk dalam harga grup/paket
        if (in_array($this->item_type, ['package_item', 'package_extra', 'custom_header'])) {
            return 0;
        }

        // Item tipe package → harga paket dikali jumlah
        if ($this->item_type === 'package' && $this->paketKatering) {
            return (float) $this->paketKatering->harga * $this->jumlah;
        }

        // Item produk harian / tambahan event
        $basePrice = $this->produk
            ? (float) $this->produk->harga
            : ($this->opsiKustom ? (float) $this->opsiKustom->harga : 0);

        $extrasPrice = 0;
        if (!empty($this->extras)) {
            $extraIds = array_column($this->extras, 'id');
            $extras = \App\Models\OpsiKustom::whereIn('id', $extraIds)->get()->keyBy('id');
            
            foreach ($this->extras as $extraData) {
                if ($extra = $extras->get($extraData['id'])) {
                    $extrasPrice += ((float) $extra->harga * $extraData['qty']);
                }
            }
        }

        return ($basePrice * $this->jumlah) + $extrasPrice;
    }

    /**
     * Sinkronisasi data menu/admin dan keranjang pelanggan (belum checkout).
     * Menghapus item yang kadaluwarsa, menu/paket/komponen yang dihapus/tidak tersedia, dan menu yang habis.
     */
    /**
         * Membersihkan keranjang pengguna dari item yang sudah tidak valid.
         * Proses bisnis: Menghapus item harian yang tanggal pemesanannya lewat, 
         * menu/paket yang dinonaktifkan admin, atau menu yang kehabisan stok.
         * @param int|null $userId
         * @param bool $flashNotification
         * @return void
         */
    public static function cleanupInvalidAndExpiredItems($userId = null, bool $flashNotification = true): void
    {
        $userId = $userId ?: auth()->id();
        if (!$userId) {
            return;
        }

        $userCarts = self::where('user_id', $userId)->get();
        if ($userCarts->isEmpty()) {
            return;
        }

        $today = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // Pre-fetch lookup maps (bulk queries untuk menghindari loop query)
        $productIds = $userCarts->pluck('produk_id')->filter()->unique();
        $activeProducts = $productIds->isNotEmpty()
            ? \App\Models\Produk::whereIn('id', $productIds)->where('is_active', true)->pluck('id')->flip()
            : collect();

        $optionIds = collect();
        foreach ($userCarts as $c) {
            if ($c->opsi_kustom_id) $optionIds->push($c->opsi_kustom_id);
            if ($c->serving_type_id) $optionIds->push($c->serving_type_id);
            if (!empty($c->extras) && is_array($c->extras)) {
                foreach ($c->extras as $extra) {
                    if (!empty($extra['id'])) $optionIds->push($extra['id']);
                }
            }
        }
        $optionIds = $optionIds->unique();
        $existingOptions = $optionIds->isNotEmpty()
            ? \App\Models\OpsiKustom::whereIn('id', $optionIds)->pluck('id')->flip()
            : collect();

        $packageIds = $userCarts->pluck('catering_package_id')->filter()->unique();
        $activePackages = $packageIds->isNotEmpty()
            ? \App\Models\PaketKatering::whereIn('id', $packageIds)->where('is_active', true)->pluck('id')->flip()
            : collect();

        // Lookup ItemPeriodeMenu untuk Katering Harian
        $keranjangHarianWithDate = $userCarts->whereNull('cart_group_id')->whereNotNull('produk_id')->whereNotNull('menu_date');
        $menuPeriodMap = collect();
        if ($keranjangHarianWithDate->isNotEmpty()) {
            $pIds = $keranjangHarianWithDate->pluck('produk_id')->unique();
            $dates = $keranjangHarianWithDate->pluck('menu_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique();
            $mpItems = \App\Models\ItemPeriodeMenu::whereIn('produk_id', $pIds)->whereIn('menu_date', $dates)->get();
            foreach ($mpItems as $mpi) {
                $dateStr = \Carbon\Carbon::parse($mpi->menu_date)->format('Y-m-d');
                $menuPeriodMap->put("{$mpi->produk_id}_{$dateStr}", $mpi);
            }
        }

        $expiredIds = [];
        $unavailableHarianIds = [];
        $habisHarianIds = [];
        $unavailableGroupIds = [];

        // 1. Cek Katering Harian (cart_group_id IS NULL)
        foreach ($userCarts->whereNull('cart_group_id') as $keranjang) {
            // Cek jadwal lewat (kadaluwarsa)
            if ($keranjang->menu_date) {
                $dateStr = \Carbon\Carbon::parse($keranjang->menu_date)->format('Y-m-d');
                if ($dateStr < $today) {
                    $expiredIds[] = $keranjang->id;
                    continue;
                }
            }

            // Cek produk deleted/inactive atau null (Jika produk menu)
            if (!$keranjang->produk_id || !isset($activeProducts[$keranjang->produk_id])) {
                $unavailableHarianIds[] = $keranjang->id;
                continue;
            }

            // Cek apakah ada komponen extra yang dihapus admin
            $hasDeletedExtra = false;
            if (!empty($keranjang->extras) && is_array($keranjang->extras)) {
                foreach ($keranjang->extras as $extra) {
                    if (!empty($extra['id']) && !isset($existingOptions[$extra['id']])) {
                        $hasDeletedExtra = true;
                        break;
                    }
                }
            }
            if ($hasDeletedExtra) {
                $unavailableHarianIds[] = $keranjang->id;
                continue;
            }

            // Cek ItemPeriodeMenu (ketersediaan/habis di jadwal)
            if ($keranjang->menu_date) {
                $dateStr = \Carbon\Carbon::parse($keranjang->menu_date)->format('Y-m-d');
                $mpi = $menuPeriodMap->get("{$keranjang->produk_id}_{$dateStr}");
                if (!$mpi) {
                    // Menu sudah tidak ada di jadwal periode aktif
                    $unavailableHarianIds[] = $keranjang->id;
                    continue;
                } elseif ($mpi->isOutOfStock()) {
                    // Status menu diubah menjadi Habis
                    $habisHarianIds[] = $keranjang->id;
                    continue;
                }
            }
        }

        // 2. Cek Katering Event (cart_group_id IS NOT NULL)
        $grupAcara = $userCarts->whereNotNull('cart_group_id')->groupBy('cart_group_id');
        foreach ($grupAcara as $groupId => $groupItems) {
            $packageRow = $groupItems->firstWhere('item_type', 'package');
            if ($packageRow) {
                // Paket Event
                // Cek apakah paket dihapus/inactive
                if (!$packageRow->catering_package_id || !isset($activePackages[$packageRow->catering_package_id])) {
                    $unavailableGroupIds[] = $groupId;
                    continue;
                }
                // Cek apakah ada menu dalam paket yang dihapus
                $packageItems = $groupItems->where('item_type', 'package_item');
                foreach ($packageItems as $pi) {
                    if (!$pi->opsi_kustom_id || !isset($existingOptions[$pi->opsi_kustom_id])) {
                        $unavailableGroupIds[] = $groupId;
                        break;
                    }
                }
                if (in_array($groupId, $unavailableGroupIds)) continue;

                // Cek penyajian
                if ($packageRow->serving_type_id && !isset($existingOptions[$packageRow->serving_type_id])) {
                    $unavailableGroupIds[] = $groupId;
                    continue;
                }
            } else {
                // Custom Menu Event
                // Cek apakah ada komponen Menu, Extra, Penyajian, atau komponen lain yang dihapus admin
                $groupBroken = false;
                $hasCustomMenu = false;

                foreach ($groupItems as $item) {
                    if (in_array($item->item_type, ['custom_menu', 'addition'])) {
                        if ($item->item_type === 'custom_menu') $hasCustomMenu = true;
                        if (!$item->opsi_kustom_id || !isset($existingOptions[$item->opsi_kustom_id])) {
                            $groupBroken = true;
                            break;
                        }
                    }
                    if ($item->serving_type_id && !isset($existingOptions[$item->serving_type_id])) {
                        $groupBroken = true;
                        break;
                    }
                    if (!empty($item->extras) && is_array($item->extras)) {
                        foreach ($item->extras as $extra) {
                            if (!empty($extra['id']) && !isset($existingOptions[$extra['id']])) {
                                $groupBroken = true;
                                break 2;
                            }
                        }
                    }
                }

                if ($groupBroken || !$hasCustomMenu) {
                    $unavailableGroupIds[] = $groupId;
                    continue;
                }
            }
        }

        // Eksekusi bulk delete
        $expiredCount = count($expiredIds);
        $unavailableCount = count($unavailableHarianIds) + count($unavailableGroupIds);
        $habisCount = count($habisHarianIds);

        if ($expiredCount > 0) {
            self::whereIn('id', $expiredIds)->delete();
        }
        if ($unavailableCount > 0) {
            if (!empty($unavailableHarianIds)) {
                self::whereIn('id', $unavailableHarianIds)->delete();
            }
            if (!empty($unavailableGroupIds)) {
                self::whereIn('cart_group_id', $unavailableGroupIds)->delete();
            }
        }
        if ($habisCount > 0) {
            self::whereIn('id', $habisHarianIds)->delete();
        }

        // Kirim notifikasi via flash message session (jika ada yang dihapus)
        if ($flashNotification && function_exists('session') && ($expiredCount > 0 || $unavailableCount > 0 || $habisCount > 0)) {
            $infoMessages = [];
            if ($expiredCount > 0) {
                $infoMessages[] = $expiredCount === 1
                    ? '1 menu harian telah dihapus dari keranjang karena jadwal pemesanannya telah berakhir.'
                    : "{$expiredCount} menu harian telah dihapus dari keranjang karena jadwal pemesanannya telah berakhir.";
            }
            if ($unavailableCount > 0) {
                $infoMessages[] = $unavailableCount === 1
                    ? '1 item telah dihapus dari keranjang karena sudah tidak tersedia.'
                    : "{$unavailableCount} item telah dihapus dari keranjang karena sudah tidak tersedia.";
            }
            if (!empty($infoMessages)) {
                session()->flash('info', implode(' ', $infoMessages));
            }

            if ($habisCount > 0) {
                $warningMsg = $habisCount === 1
                    ? '1 menu harian telah dihapus dari keranjang karena menu sudah habis.'
                    : "{$habisCount} menu harian telah dihapus dari keranjang karena menu sudah habis.";
                session()->flash('warning', $warningMsg);
            }
        }
    }

    /**
     * Hapus otomatis item Katering Harian yang sudah melewati jadwal pemesanan (menu_date < hari ini).
     *
     * @param int|null $userId
     * @return int
     */
    public static function removeExpiredHarianItems($userId = null, bool $flashNotification = true): int
    {
        self::cleanupInvalidAndExpiredItems($userId, $flashNotification);
        return 0;
    }
}

