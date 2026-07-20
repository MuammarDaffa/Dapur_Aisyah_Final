<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananKatering;
use App\Models\OpsiKustom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CateringController extends Controller
{
    /**
     * Daftar semua katering dengan filter pencarian dan tipe.
     */
    public function index(Request $request)
    {
        $query = LayananKatering::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            if ($request->type === 'harian') {
                $query->daily();
            } elseif ($request->type === 'acara') {
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
        $isDaily = $request->input('catering_type') === 'harian';
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'catering_type' => 'required|in:daily,event',
            'serving_types' => 'nullable|array',
            'min_portion' => $isDaily ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'maksimal_porsi' => 'nullable|integer|min:1',
            'base_price' => 'required|numeric|min:0|max:1000000000',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'minimal_order_days' => 'nullable|integer|min:0',
            'service_area' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ], [
            'base_price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        if ($isDaily) {
            $validated['min_portion'] = $validated['min_portion'] ?? 1;
            $validated['maksimal_porsi'] = null;
            $validated['order_terms'] = null;
            $validated['schedule_notes'] = null;
            $validated['minimal_order_days'] = null;
        }

        // Set fitur_tersedia berdasarkan tipe
        $validated['fitur_tersedia'] = $request->catering_type === 'harian'
            ? ['daily_menu']
            : ['packages', 'full_custom'];

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        unset($validated['catering_type']);

        LayananKatering::create($validated);

        return redirect()->route('admin.catering.index')->with('success', 'Katering berhasil ditambahkan.');
    }

    /**
     * Detail Katering — menampilkan Produk (Daily) atau Paket+Menu+Penyajian+Extra (Event).
     */
    public function show(LayananKatering $catering)
    {
        if ($catering->isDaily()) {
            $produk = $catering->produk()->latest()->paginate(10);
            $allProducts = $catering->produk()->active()->get();
            $extras = $catering->opsiKustom()->where('type', 'extra')->get();
            $currentSchedule = $catering->periodeMenu()
                ->with(['items' => fn($q) => $q->orderBy('menu_date')])
                ->withCount('items')
                ->latest('start_date')
                ->first();

            return view('admin.catering.show-harian', compact('catering', 'produk', 'allProducts', 'extras', 'currentSchedule'));
        }

        if ($catering->isEvent()) {
            $packages = $catering->packages()->with('opsiKustom')->latest()->paginate(10);
            $menus = $catering->opsiKustom()->where('type', 'menu')->get();
            $servings = \App\Models\OpsiKustom::where('type', 'tipe_penyajian')->get();
            $extras = $catering->opsiKustom()->where('type', 'extra')->get();

            return view('admin.catering.show-acara', compact('catering', 'packages', 'menus', 'servings', 'extras'));
        }

        return redirect()->route('admin.catering.index')
            ->with('error', 'Tipe katering tidak dikenali.');
    }

    /**
     * Form edit katering.
     */
    public function edit(LayananKatering $catering)
    {
        return view('admin.catering.edit', compact('catering'));
    }

    /**
     * Update katering — tipe tidak dapat diubah.
     */
    public function update(Request $request, LayananKatering $catering)
    {
        $isDaily = $catering->isDaily();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'min_portion' => $isDaily ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'maksimal_porsi' => 'nullable|integer|min:1',
            'base_price' => 'required|numeric|min:0|max:1000000000',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'minimal_order_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ], [
            'base_price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        if ($isDaily) {
            $validated['min_portion'] = $validated['min_portion'] ?? $catering->min_portion ?? 1;
            $validated['maksimal_porsi'] = null;
            $validated['order_terms'] = null;
            $validated['schedule_notes'] = null;
            $validated['minimal_order_days'] = null;
        }

        // Tipe katering tidak boleh diubah — pertahankan fitur_tersedia yang ada
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
    public function destroy(LayananKatering $catering)
    {
        try {
            // Hapus pesanan terkait agar tidak terjadi error foreign key
            \App\Models\Pesanan::where('layanan_katering_id', $catering->id)->delete();

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
    public function storeOption(Request $request, LayananKatering $catering)
    {
        $validated = $request->validate([
            'type' => 'required|in:menu,extra',
            'name' => 'required|string|max:150',
            'harga' => 'required|numeric|min:0|max:1000000000',
            'is_active' => 'boolean',
            'items' => 'exclude_unless:type,menu|required|array|min:1',
            'items.*' => 'required|string|max:150',
            'image' => 'nullable|image|max:2048',
        ], [
            'harga.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
            'items.required' => 'Minimal 1 item menu harus ditambahkan.',
            'items.min' => 'Minimal 1 item menu harus ditambahkan.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $validated['layanan_katering_id'] = $catering->id;
        $validated['is_active'] = $request->boolean('is_active');
        
        // Remove items array if type is not menu, though exclude_unless handles this.
        if ($request->type !== 'menu') {
            $validated['items'] = null;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('opsi_kustom', 'public');
        }

        OpsiKustom::create($validated);

        return redirect()->route('admin.catering.show', $catering)
            ->with('success', ucfirst(str_replace('_', ' ', $validated['type'])) . ' berhasil ditambahkan.');
    }

    /**
     * Update option milik katering tertentu.
     */
    public function updateOption(Request $request, LayananKatering $catering, OpsiKustom $option)
    {
        // Pastikan option milik katering ini
        abort_if($option->layanan_katering_id !== $catering->id, 403, 'Option bukan milik katering ini.');
        abort_if($option->type === 'tipe_penyajian', 403, 'Data Penyajian adalah master data tetap dan tidak dapat diubah.');

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'harga' => 'required|numeric|min:0|max:1000000000',
            'is_active' => 'boolean',
            'items' => 'nullable|array',
            'items.*' => 'required|string|max:150',
            'image' => 'nullable|image|max:2048',
        ], [
            'harga.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
            'items.required' => 'Minimal 1 item menu harus ditambahkan.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Validate items specifically if it's a menu
        if ($option->type === 'menu') {
            $request->validate([
                'items' => 'required|array|min:1',
            ], [
                'items.required' => 'Minimal 1 item menu harus ditambahkan.',
                'items.min' => 'Minimal 1 item menu harus ditambahkan.',
            ]);
        } else {
            $validated['items'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($option->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($option->image);
            }
            $validated['image'] = $request->file('image')->store('opsi_kustom', 'public');
        }

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
    public function destroyOption(LayananKatering $catering, OpsiKustom $option)
    {
        // Pastikan option milik katering ini
        abort_if($option->layanan_katering_id !== $catering->id, 403, 'Option bukan milik katering ini.');
        abort_if($option->type === 'tipe_penyajian', 403, 'Data Penyajian adalah master data tetap dan tidak dapat dihapus.');

        $option->delete();

        return redirect()->route('admin.catering.show', $catering)
            ->with('success', 'Data berhasil dihapus.');
    }
}
