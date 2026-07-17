<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'catering_service_id', 'cart_group_id', 'product_id',
        'custom_option_id', 'catering_package_id', 'extras',
        'quantity', 'item_type', 'serving_type_id', 'menu_date', 'notes',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customOption(): BelongsTo
    {
        return $this->belongsTo(CustomOption::class);
    }

    public function cateringPackage(): BelongsTo
    {
        return $this->belongsTo(CateringPackage::class);
    }

    public function cateringService(): BelongsTo
    {
        return $this->belongsTo(CateringService::class);
    }

    public function servingType(): BelongsTo
    {
        return $this->belongsTo(CustomOption::class, 'serving_type_id');
    }

    // === Helpers ===

    /**
     * Cek apakah item ini adalah bagian dari event order.
     */
    public function isEventItem(): bool
    {
        return !empty($this->cart_group_id);
    }

    /**
     * Cek apakah item ini adalah harian (bukan event).
     */
    public function isDailyItem(): bool
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
    public function getSubtotalAttribute(): float
    {
        // Item paket (isi paket atau extra paket) atau custom_header → harga 0, sudah termasuk dalam harga grup/paket
        if (in_array($this->item_type, ['package_item', 'package_extra', 'custom_header'])) {
            return 0;
        }

        // Item tipe package → harga paket dikali quantity
        if ($this->item_type === 'package' && $this->cateringPackage) {
            return (float) $this->cateringPackage->price * $this->quantity;
        }

        // Item produk harian / tambahan event
        $basePrice = $this->product
            ? (float) $this->product->price
            : ($this->customOption ? (float) $this->customOption->price : 0);

        $extrasPrice = 0;
        if (!empty($this->extras)) {
            $extraIds = array_column($this->extras, 'id');
            $extras = \App\Models\CustomOption::whereIn('id', $extraIds)->get()->keyBy('id');
            
            foreach ($this->extras as $extraData) {
                if ($extra = $extras->get($extraData['id'])) {
                    $extrasPrice += ((float) $extra->price * $extraData['qty']);
                }
            }
        }

        return ($basePrice * $this->quantity) + $extrasPrice;
    }

    /**
     * Sinkronisasi data menu/admin dan keranjang pelanggan (belum checkout).
     * Menghapus item yang kadaluwarsa, menu/paket/komponen yang dihapus/tidak tersedia, dan menu yang habis.
     */
    public static function cleanupInvalidAndExpiredItems($userId = null): void
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
        $productIds = $userCarts->pluck('product_id')->filter()->unique();
        $activeProducts = $productIds->isNotEmpty()
            ? \App\Models\Product::whereIn('id', $productIds)->where('is_active', true)->pluck('id')->flip()
            : collect();

        $optionIds = collect();
        foreach ($userCarts as $c) {
            if ($c->custom_option_id) $optionIds->push($c->custom_option_id);
            if ($c->serving_type_id) $optionIds->push($c->serving_type_id);
            if (!empty($c->extras) && is_array($c->extras)) {
                foreach ($c->extras as $extra) {
                    if (!empty($extra['id'])) $optionIds->push($extra['id']);
                }
            }
        }
        $optionIds = $optionIds->unique();
        $existingOptions = $optionIds->isNotEmpty()
            ? \App\Models\CustomOption::whereIn('id', $optionIds)->pluck('id')->flip()
            : collect();

        $packageIds = $userCarts->pluck('catering_package_id')->filter()->unique();
        $activePackages = $packageIds->isNotEmpty()
            ? \App\Models\CateringPackage::whereIn('id', $packageIds)->where('is_active', true)->pluck('id')->flip()
            : collect();

        // Lookup MenuPeriodItem untuk Katering Harian
        $dailyCartsWithDate = $userCarts->whereNull('cart_group_id')->whereNotNull('product_id')->whereNotNull('menu_date');
        $menuPeriodMap = collect();
        if ($dailyCartsWithDate->isNotEmpty()) {
            $pIds = $dailyCartsWithDate->pluck('product_id')->unique();
            $dates = $dailyCartsWithDate->pluck('menu_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique();
            $mpItems = \App\Models\MenuPeriodItem::whereIn('product_id', $pIds)->whereIn('menu_date', $dates)->get();
            foreach ($mpItems as $mpi) {
                $dateStr = \Carbon\Carbon::parse($mpi->menu_date)->format('Y-m-d');
                $menuPeriodMap->put("{$mpi->product_id}_{$dateStr}", $mpi);
            }
        }

        $expiredIds = [];
        $unavailableDailyIds = [];
        $habisDailyIds = [];
        $unavailableGroupIds = [];

        // 1. Cek Katering Harian (cart_group_id IS NULL)
        foreach ($userCarts->whereNull('cart_group_id') as $cart) {
            // Cek jadwal lewat (kadaluwarsa)
            if ($cart->menu_date) {
                $dateStr = \Carbon\Carbon::parse($cart->menu_date)->format('Y-m-d');
                if ($dateStr < $today) {
                    $expiredIds[] = $cart->id;
                    continue;
                }
            }

            // Cek produk deleted/inactive atau null (Jika produk menu)
            if (!$cart->product_id || !isset($activeProducts[$cart->product_id])) {
                $unavailableDailyIds[] = $cart->id;
                continue;
            }

            // Cek apakah ada komponen extra yang dihapus admin
            $hasDeletedExtra = false;
            if (!empty($cart->extras) && is_array($cart->extras)) {
                foreach ($cart->extras as $extra) {
                    if (!empty($extra['id']) && !isset($existingOptions[$extra['id']])) {
                        $hasDeletedExtra = true;
                        break;
                    }
                }
            }
            if ($hasDeletedExtra) {
                $unavailableDailyIds[] = $cart->id;
                continue;
            }

            // Cek MenuPeriodItem (ketersediaan/habis di jadwal)
            if ($cart->menu_date) {
                $dateStr = \Carbon\Carbon::parse($cart->menu_date)->format('Y-m-d');
                $mpi = $menuPeriodMap->get("{$cart->product_id}_{$dateStr}");
                if (!$mpi) {
                    // Menu sudah tidak ada di jadwal periode aktif
                    $unavailableDailyIds[] = $cart->id;
                    continue;
                } elseif ($mpi->isOutOfStock()) {
                    // Status menu diubah menjadi Habis
                    $habisDailyIds[] = $cart->id;
                    continue;
                }
            }
        }

        // 2. Cek Katering Event (cart_group_id IS NOT NULL)
        $eventGroups = $userCarts->whereNotNull('cart_group_id')->groupBy('cart_group_id');
        foreach ($eventGroups as $groupId => $groupItems) {
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
                    if (!$pi->custom_option_id || !isset($existingOptions[$pi->custom_option_id])) {
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
                        if (!$item->custom_option_id || !isset($existingOptions[$item->custom_option_id])) {
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
        $unavailableCount = count($unavailableDailyIds) + count($unavailableGroupIds);
        $habisCount = count($habisDailyIds);

        if ($expiredCount > 0) {
            self::whereIn('id', $expiredIds)->delete();
        }
        if ($unavailableCount > 0) {
            if (!empty($unavailableDailyIds)) {
                self::whereIn('id', $unavailableDailyIds)->delete();
            }
            if (!empty($unavailableGroupIds)) {
                self::whereIn('cart_group_id', $unavailableGroupIds)->delete();
            }
        }
        if ($habisCount > 0) {
            self::whereIn('id', $habisDailyIds)->delete();
        }

        // Kirim notifikasi via flash message session (jika ada yang dihapus)
        if (function_exists('session') && ($expiredCount > 0 || $unavailableCount > 0 || $habisCount > 0)) {
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
    public static function removeExpiredHarianItems($userId = null): int
    {
        self::cleanupInvalidAndExpiredItems($userId);
        return 0;
    }
}

