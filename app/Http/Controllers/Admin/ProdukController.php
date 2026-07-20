<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananKatering;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('layananKatering');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('service')) {
            $query->where('layanan_katering_id', $request->service);
        }

        $produk = $query->latest()->paginate(15);
        $services = LayananKatering::all();

        return view('admin.produk.index', compact('produk', 'services'));
    }

    public function create(Request $request)
    {
        $services = LayananKatering::active()->get();
        $extras = [];
        if ($request->has('layanan_katering_id')) {
            $extras = \App\Models\OpsiKustom::where('layanan_katering_id', $request->layanan_katering_id)
                        ->where('type', 'extra')->active()->get();
        }
        return view('admin.produk.create', compact('services', 'extras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'layanan_katering_id' => 'required|exists:layanan_katering,id',
            'name' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0|max:1000000000',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:opsi_kustom,id',
        ], [
            'harga.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('produk', 'public');
        }

        $produk = Produk::create($validated);

        if ($request->has('extras')) {
            $produk->extras()->sync($request->extras);
        }

        return redirect()->route('admin.catering.show', $validated['layanan_katering_id'])
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $services = LayananKatering::active()->get();
        $extras = \App\Models\OpsiKustom::where('layanan_katering_id', $produk->layanan_katering_id)
                        ->where('type', 'extra')->active()->get();
        return view('admin.produk.edit', compact('produk', 'services', 'extras'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'layanan_katering_id' => 'required|exists:layanan_katering,id',
            'name' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0|max:1000000000',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:opsi_kustom,id',
        ], [
            'harga.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('produk', 'public');
        }

        $produk->update($validated);
        
        $syncResult = $produk->extras()->sync($request->extras ?? []);
        $wasSyncChanged = !empty($syncResult['attached']) || !empty($syncResult['detached']) || !empty($syncResult['updated']);

        if (!$produk->wasChanged() && !$wasSyncChanged) {
            return redirect()->route('admin.catering.show', $produk->layanan_katering_id);
        }

        return redirect()->route('admin.catering.show', $produk->layanan_katering_id)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $cateringId = $produk->layanan_katering_id;
        $produk->delete();
        return redirect()->route('admin.catering.show', $cateringId)
            ->with('success', 'Produk berhasil dihapus.');
    }
}
