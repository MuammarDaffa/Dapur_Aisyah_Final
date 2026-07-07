<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringService;
use App\Models\MenuPeriod;
use App\Models\MenuPeriodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuPeriodController extends Controller
{
    /**
     * Halaman Editor Jadwal Menu Mingguan untuk layanan harian tertentu.
     * Hanya ada satu jadwal yang disimpan per layanan katering harian.
     */
    public function index(CateringService $catering)
    {
        return redirect()->route('admin.catering.show', $catering);
    }

    /**
     * Simpan/Perbarui jadwal menu mingguan beserta seluruh baris tanggalnya.
     */
    public function store(Request $request, CateringService $catering)
    {
        abort_if(!$catering->isDaily(), 404);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'items' => 'required|array|min:1',
            'items.*.menu_date' => 'required|date',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.status' => 'required|in:tersedia,habis',
        ], [
            'items.required' => 'Silakan klik tombol Buat Jadwal dan pilih produk untuk setiap tanggal terlebih dahulu.',
            'items.min' => 'Jadwal minimal harus memiliki 1 hari.',
            'items.*.product_id.required' => 'Silakan pilih produk menu untuk semua tanggal yang tersedia.',
            'items.*.status.required' => 'Silakan pilih status produk untuk semua tanggal.',
        ]);

        // Validasi bahwa semua produk milik layanan katering ini
        $productIds = collect($request->items)->pluck('product_id')->unique();
        $validProductsCount = $catering->products()->whereIn('id', $productIds)->count();
        if ($validProductsCount !== $productIds->count()) {
            return back()->withInput()->with('error', 'Terdapat produk yang dipilih bukan milik layanan katering ini.');
        }

        DB::transaction(function () use ($catering, $request) {
            $periods = $catering->menuPeriods()->latest('start_date')->get();
            $period = $periods->first();

            // Jika ada lebih dari 1 periode lama dari sistem sebelumnya, hapus sisanya agar tepat 1 jadwal
            if ($periods->count() > 1) {
                $periods->slice(1)->each->delete();
            }

            if ($period) {
                // Update periode existing
                $period->update([
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_active' => true,
                ]);
                // Hapus item lama agar digantikan daftar terbaru (menghindari duplikasi)
                $period->items()->delete();
            } else {
                // Buat periode baru
                $period = MenuPeriod::create([
                    'catering_service_id' => $catering->id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_active' => true,
                ]);
            }

            // Siapkan data item untuk insert serentak
            $itemsData = [];
            $now = now();
            foreach ($request->items as $item) {
                $itemsData[] = [
                    'menu_period_id' => $period->id,
                    'product_id' => $item['product_id'],
                    'menu_date' => $item['menu_date'],
                    'status' => $item['status'] ?? 'tersedia',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            MenuPeriodItem::insert($itemsData);
        });

        return redirect()->route('admin.catering.show', $catering)
            ->with('success', 'Jadwal menu mingguan berhasil disimpan.');
    }
}
