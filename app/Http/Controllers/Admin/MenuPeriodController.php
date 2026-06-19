<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringService;
use App\Models\MenuPeriod;
use App\Models\MenuPeriodItem;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class MenuPeriodController extends Controller
{
    /**
     * Daftar periode menu untuk layanan harian tertentu.
     */
    public function index(CateringService $catering)
    {
        abort_if(!$catering->isDaily(), 404, 'Menu Mingguan hanya untuk layanan harian.');

        $periods = $catering->menuPeriods()
            ->withCount('items')
            ->latest('start_date')
            ->paginate(10);

        return view('admin.catering.menu-periods.index', compact('catering', 'periods'));
    }

    /**
     * Simpan periode menu baru.
     */
    public function store(Request $request, CateringService $catering)
    {
        abort_if(!$catering->isDaily(), 404);

        $validated = $request->validate([
            'nama_periode' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['catering_service_id'] = $catering->id;
        $validated['is_active'] = true;

        $period = MenuPeriod::create($validated);

        return redirect()->route('admin.menu-periods.show', [$catering, $period])
            ->with('success', 'Periode menu berhasil dibuat. Silakan assign produk per tanggal.');
    }

    /**
     * Detail periode — assign produk per tanggal.
     */
    public function show(CateringService $catering, MenuPeriod $period)
    {
        abort_if($period->catering_service_id !== $catering->id, 403);

        $period->load(['items.product']);

        // Generate daftar tanggal dalam range yang belum di-assign
        $allDates = CarbonPeriod::create($period->start_date, $period->end_date);
        $assignedDates = $period->items->pluck('menu_date')->map(fn ($d) => $d->format('Y-m-d'))->toArray();

        $availableDates = [];
        foreach ($allDates as $date) {
            if (!in_array($date->format('Y-m-d'), $assignedDates)) {
                $availableDates[] = $date->copy();
            }
        }

        // Produk aktif milik layanan ini
        $products = $catering->products()->active()->get();

        return view('admin.catering.menu-periods.show', compact('catering', 'period', 'availableDates', 'products'));
    }

    /**
     * Update periode menu.
     */
    public function update(Request $request, CateringService $catering, MenuPeriod $period)
    {
        abort_if($period->catering_service_id !== $catering->id, 403);

        $validated = $request->validate([
            'nama_periode' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $period->update($validated);

        return redirect()->route('admin.menu-periods.show', [$catering, $period])
            ->with('success', 'Periode menu berhasil diperbarui.');
    }

    /**
     * Hapus periode menu.
     */
    public function destroy(CateringService $catering, MenuPeriod $period)
    {
        abort_if($period->catering_service_id !== $catering->id, 403);

        $period->delete();

        return redirect()->route('admin.menu-periods.index', $catering)
            ->with('success', 'Periode menu berhasil dihapus.');
    }

    /**
     * Assign produk ke tanggal dalam periode.
     */
    public function assignProduct(Request $request, CateringService $catering, MenuPeriod $period)
    {
        abort_if($period->catering_service_id !== $catering->id, 403);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'menu_date' => 'required|date',
        ]);

        // Validasi tanggal dalam range periode
        $menuDate = Carbon::parse($validated['menu_date']);
        if ($menuDate->lt($period->start_date) || $menuDate->gt($period->end_date)) {
            return back()->with('error', 'Tanggal harus dalam range periode.');
        }

        // Cek apakah tanggal sudah di-assign
        $exists = $period->items()->where('menu_date', $validated['menu_date'])->exists();
        if ($exists) {
            return back()->with('error', 'Tanggal ini sudah memiliki produk yang di-assign.');
        }

        // Cek produk milik layanan ini
        $product = Product::find($validated['product_id']);
        if ($product->catering_service_id !== $catering->id) {
            return back()->with('error', 'Produk bukan milik layanan ini.');
        }

        MenuPeriodItem::create([
            'menu_period_id' => $period->id,
            'product_id' => $validated['product_id'],
            'menu_date' => $validated['menu_date'],
        ]);

        return redirect()->route('admin.menu-periods.show', [$catering, $period])
            ->with('success', 'Produk berhasil di-assign ke tanggal.');
    }

    /**
     * Hapus assign produk dari tanggal.
     */
    public function removeProduct(CateringService $catering, MenuPeriod $period, MenuPeriodItem $item)
    {
        abort_if($period->catering_service_id !== $catering->id, 403);
        abort_if($item->menu_period_id !== $period->id, 403);

        $item->delete();

        return redirect()->route('admin.menu-periods.show', [$catering, $period])
            ->with('success', 'Produk berhasil dihapus dari tanggal.');
    }
}
