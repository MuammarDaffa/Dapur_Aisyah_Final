<?php

namespace App\Observers;

use App\Models\DetailPesanan;

class DetailPesananObserver
{
    /**
     * Handle the DetailPesanan "created" event.
     */
    public function created(DetailPesanan $detailPesanan): void
    {
        if ($detailPesanan->menu_harian_id) {
            $menu = $detailPesanan->menuHarian;
            if ($menu && $menu->stok_tersisa !== null) {
                $menu->stok_tersisa -= $detailPesanan->jumlah;
                $menu->save();
            }
        }
    }

    /**
     * Handle the DetailPesanan "updated" event.
     */
    public function updated(DetailPesanan $detailPesanan): void
    {
        if ($detailPesanan->menu_harian_id && $detailPesanan->isDirty('jumlah')) {
            $menu = $detailPesanan->menuHarian;
            if ($menu && $menu->stok_tersisa !== null) {
                $oldJumlah = $detailPesanan->getOriginal('jumlah');
                $newJumlah = $detailPesanan->jumlah;
                
                $diff = $newJumlah - $oldJumlah;
                $menu->stok_tersisa -= $diff;
                $menu->save();
            }
        }
    }

    /**
     * Handle the DetailPesanan "deleted" event.
     */
    public function deleted(DetailPesanan $detailPesanan): void
    {
        if ($detailPesanan->menu_harian_id) {
            $menu = $detailPesanan->menuHarian;
            if ($menu && $menu->stok_tersisa !== null) {
                $menu->stok_tersisa += $detailPesanan->jumlah;
                $menu->save();
            }
        }
    }
}
