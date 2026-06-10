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
            'base_price' => 'required|numeric|min:0',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'service_area' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Set available_features berdasarkan tipe
        $validated['available_features'] = $request->catering_type === 'daily'
            ? ['daily_menu']
            : ['packages'];

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
     * Detail Katering — menampilkan Produk (Daily) atau Paket (Event).
     */
    public function show(CateringService $catering)
    {
        if ($catering->isDaily()) {
            $products = $catering->products()->latest()->paginate(10);
            $extras = $catering->extras()->active()->get();

            return view('admin.catering.show-daily', compact('catering', 'products', 'extras'));
        }

        if ($catering->isEvent()) {
            $packages = $catering->packages()->with('customOptions')->latest()->paginate(10);

            return view('admin.catering.show-event', compact('catering', 'packages'));
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
     * Update katering.
     */
    public function update(Request $request, CateringService $catering)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'catering_type' => 'required|in:daily,event',
            'serving_types' => 'nullable|array',
            'min_portion' => 'required|integer|min:1',
            'max_portion' => 'nullable|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'service_area' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Set available_features berdasarkan tipe
        $validated['available_features'] = $request->catering_type === 'daily'
            ? ['daily_menu']
            : ['packages'];

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        unset($validated['catering_type']);

        $catering->update($validated);

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
}
