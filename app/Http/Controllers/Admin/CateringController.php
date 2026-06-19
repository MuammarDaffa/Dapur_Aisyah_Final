<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringService;
use App\Models\CustomOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CateringController extends Controller
{
    /**
     * Daftar semua katering dengan filter pencarian dan tipe.
     */
    public function index(Request $request)
    {
        $query = CateringService::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            if ($request->type === 'daily') {
                $query->daily();
            } elseif ($request->type === 'event') {
                $query->event();
            }
        }

        $caterings = $query->latest()->paginate(10);

        return view('admin.catering.index', compact('caterings'));
    }

    /**
     * Form tambah katering baru.
     */
    public function create()
    {
        return view('admin.catering.create');
    }

    /**
     * Simpan katering baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'catering_type' => 'required|in:daily,event',
            'serving_types' => 'nullable|array',
            'min_portion' => 'required|integer|min:1',
            'max_portion' => 'nullable|integer|min:1',
            'base_price' => 'required|numeric|min:0|max:1000000000',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'minimal_order_days' => 'nullable|integer|min:0',
            'cutoff_time' => 'nullable|date_format:H:i',
            'service_area' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ], [
            'base_price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        // Set available_features berdasarkan tipe
        $validated['available_features'] = $request->catering_type === 'daily'
            ? ['daily_menu']
            : ['packages', 'full_custom'];

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        unset($validated['catering_type']);

        CateringService::create($validated);

        return redirect()->route('admin.catering.index')->with('success', 'Katering berhasil ditambahkan.');
    }

    /**
     * Detail Katering — menampilkan Produk (Daily) atau Paket+Menu+Penyajian+Extra (Event).
     */
    public function show(CateringService $catering)
    {
        if ($catering->isDaily()) {
            $products = $catering->products()->latest()->paginate(10);
            $extras = $catering->customOptions()->where('type', 'extra')->get();
            $periods = \App\Models\MenuPeriod::withCount('items')->orderBy('start_date', 'desc')->get();

            return view('admin.catering.show-daily', compact('catering', 'products', 'extras', 'periods'));
        }

        if ($catering->isEvent()) {
            $packages = $catering->packages()->with('customOptions')->latest()->paginate(10);
            $menus = $catering->customOptions()->where('type', 'menu')->get();
            $servings = $catering->customOptions()->where('type', 'serving_type')->get();
            $extras = $catering->customOptions()->where('type', 'extra')->get();

            return view('admin.catering.show-event', compact('catering', 'packages', 'menus', 'servings', 'extras'));
        }

        return redirect()->route('admin.catering.index')
            ->with('error', 'Tipe katering tidak dikenali.');
    }

    /**
     * Form edit katering.
     */
    public function edit(CateringService $catering)
    {
        return view('admin.catering.edit', compact('catering'));
    }

    /**
     * Update katering — tipe tidak dapat diubah.
     */
    public function update(Request $request, CateringService $catering)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'min_portion' => 'required|integer|min:1',
            'max_portion' => 'nullable|integer|min:1',
            'base_price' => 'required|numeric|min:0|max:1000000000',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'minimal_order_days' => 'nullable|integer|min:0',
            'cutoff_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ], [
            'base_price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        // Tipe katering tidak boleh diubah — pertahankan available_features yang ada
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        $catering->update($validated);

        if (!$catering->wasChanged()) {
            return redirect()->route('admin.catering.index');
        }

        return redirect()->route('admin.catering.index')->with('success', 'Katering berhasil diperbarui.');
    }

    /**
     * Hapus katering beserta data terkait.
     */
    public function destroy(CateringService $catering)
    {
        try {
            // Hapus pesanan terkait agar tidak terjadi error foreign key
            \App\Models\Order::where('catering_service_id', $catering->id)->delete();

            $catering->delete();
            return redirect()->route('admin.catering.index')
                ->with('success', 'Katering berhasil dihapus beserta data yang terkait.');
        } catch (\Exception $e) {
            return redirect()->route('admin.catering.index')
                ->with('error', 'Gagal menghapus katering: ' . $e->getMessage());
        }
    }

    // =====================================================
    // Inline CRUD untuk Custom Options (Menu, Penyajian, Extra)
    // Task 13, 14, 15, 16
    // =====================================================

    /**
     * Simpan option baru (Menu/Penyajian/Extra) untuk katering tertentu.
     */
    public function storeOption(Request $request, CateringService $catering)
    {
        $validated = $request->validate([
            'type' => 'required|in:menu,serving_type,extra',
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0|max:1000000000',
            'is_active' => 'boolean',
        ], [
            'price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['catering_service_id'] = $catering->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        CustomOption::create($validated);

        return redirect()->route('admin.catering.show', $catering)
            ->with('success', ucfirst(str_replace('_', ' ', $validated['type'])) . ' berhasil ditambahkan.');
    }

    /**
     * Update option milik katering tertentu.
     */
    public function updateOption(Request $request, CateringService $catering, CustomOption $option)
    {
        // Pastikan option milik katering ini
        abort_if($option->catering_service_id !== $catering->id, 403, 'Option bukan milik katering ini.');

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0|max:1000000000',
            'is_active' => 'boolean',
        ], [
            'price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $option->update($validated);

        if (!$option->wasChanged()) {
            return redirect()->route('admin.catering.show', $catering);
        }

        return redirect()->route('admin.catering.show', $catering)
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Hapus option milik katering tertentu.
     */
    public function destroyOption(CateringService $catering, CustomOption $option)
    {
        // Pastikan option milik katering ini
        abort_if($option->catering_service_id !== $catering->id, 403, 'Option bukan milik katering ini.');

        $option->delete();

        return redirect()->route('admin.catering.show', $catering)
            ->with('success', 'Data berhasil dihapus.');
    }
}
