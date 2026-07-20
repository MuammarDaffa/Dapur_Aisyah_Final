<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketKatering;
use App\Models\LayananKatering;
use App\Models\OpsiKustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = PaketKatering::with('layananKatering');

        if ($request->filled('service')) {
            $query->where('layanan_katering_id', $request->service);
        }

        $packages = $query->latest()->paginate(15);

        // Hanya layanan yang punya fitur packages atau full_custom
        $services = LayananKatering::active()->event()->get();

        return redirect()->route('admin.dashboard')->with('error', 'Silakan akses paket dari menu layanan katering.');
    }

    public function create()
    {
        $services = LayananKatering::active()->event()->get();
        return view('admin.packages.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'layanan_katering_id' => 'required|exists:layanan_katering,id',
            'name' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0|max:1000000000',
            'total_portions' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'benefits' => 'nullable|array',
            'benefits.*' => 'nullable|string|max:255',
            'min_addition_qty' => 'nullable|integer|min:0',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'menu_ids' => 'nullable|array',
            'menu_ids.*' => 'exists:opsi_kustom,id',
            'serving_type_id' => 'nullable|exists:opsi_kustom,id',
            'extra_ids' => 'nullable|array',
            'extra_ids.*' => 'exists:opsi_kustom,id',
        ], [
            'harga.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['is_custom'] = $request->boolean('is_custom');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['benefits'] = array_values(array_filter($request->input('benefits', []), fn($b) => !empty(trim($b))));

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        $package = PaketKatering::create($validated);

        // Sync pivot custom options
        $syncIds = collect();
        if ($request->has('menu_ids')) {
            $syncIds = $syncIds->merge($request->input('menu_ids'));
        }
        if ($request->filled('serving_type_id')) {
            $syncIds->push($request->input('serving_type_id'));
        }
        if ($request->has('extra_ids')) {
            $syncIds = $syncIds->merge($request->input('extra_ids'));
        }

        // Simpan tanpa jumlah tambahan, gunakan default database
        $package->opsiKustom()->sync($syncIds->toArray());

        return redirect()->route('admin.catering.show', $validated['layanan_katering_id'])->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(PaketKatering $package)
    {
        $services = LayananKatering::active()->event()->get();
        $package->load('opsiKustom');
        return view('admin.packages.edit', compact('package', 'services'));
    }

    public function update(Request $request, PaketKatering $package)
    {
        $validated = $request->validate([
            'layanan_katering_id' => 'required|exists:layanan_katering,id',
            'name' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0|max:1000000000',
            'total_portions' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'benefits' => 'nullable|array',
            'benefits.*' => 'nullable|string|max:255',
            'min_addition_qty' => 'nullable|integer|min:0',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'menu_ids' => 'nullable|array',
            'menu_ids.*' => 'exists:opsi_kustom,id',
            'serving_type_id' => 'nullable|exists:opsi_kustom,id',
            'extra_ids' => 'nullable|array',
            'extra_ids.*' => 'exists:opsi_kustom,id',
        ], [
            'harga.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['is_custom'] = $request->boolean('is_custom');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['benefits'] = array_values(array_filter($request->input('benefits', []), fn($b) => !empty(trim($b))));

        if ($request->hasFile('image')) {
            if ($package->image && Storage::disk('public')->exists($package->image)) {
                Storage::disk('public')->delete($package->image);
            }
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        $package->update($validated);

        // Sync pivot custom options
        $syncIds = collect();
        if ($request->has('menu_ids')) {
            $syncIds = $syncIds->merge($request->input('menu_ids'));
        }
        if ($request->filled('serving_type_id')) {
            $syncIds->push($request->input('serving_type_id'));
        }
        if ($request->has('extra_ids')) {
            $syncIds = $syncIds->merge($request->input('extra_ids'));
        }

        $syncResult = $package->opsiKustom()->sync($syncIds->toArray());
        $wasSyncChanged = !empty($syncResult['attached']) || !empty($syncResult['detached']) || !empty($syncResult['updated']);

        if (!$package->wasChanged() && !$wasSyncChanged) {
            return redirect()->route('admin.catering.show', $package->layanan_katering_id);
        }

        return redirect()->route('admin.catering.show', $package->layanan_katering_id)->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(PaketKatering $package)
    {
        if ($package->image && Storage::disk('public')->exists($package->image)) {
            Storage::disk('public')->delete($package->image);
        }
        $serviceId = $package->layanan_katering_id;
        $package->delete();
        return redirect()->route('admin.catering.show', $serviceId)->with('success', 'Paket berhasil dihapus.');
    }
}
