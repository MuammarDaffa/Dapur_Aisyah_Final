<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringPackage;
use App\Models\CateringService;
use App\Models\CustomOption;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = CateringPackage::with('cateringService');

        if ($request->filled('service')) {
            $query->where('catering_service_id', $request->service);
        }

        $packages = $query->latest()->paginate(15);

        // Hanya layanan yang punya fitur packages atau full_custom
        $services = CateringService::active()->event()->get();

        return view('admin.packages.index', compact('packages', 'services'));
    }

    public function create()
    {
        $services = CateringService::active()->event()->get();
        return view('admin.packages.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'total_portions' => 'required|integer|min:1',
            'min_addition_qty' => 'nullable|integer|min:0',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'menu_ids' => 'nullable|array',
            'menu_ids.*' => 'exists:custom_options,id',
            'decoration_id' => 'nullable|exists:custom_options,id',
            'serving_type_id' => 'nullable|exists:custom_options,id',
            'extra_ids' => 'nullable|array',
            'extra_ids.*' => 'exists:custom_options,id',
        ]);

        $validated['is_custom'] = $request->boolean('is_custom');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_addition_qty'] = $validated['min_addition_qty'] ?? 0;

        $package = CateringPackage::create($validated);

        // Sync pivot custom options
        $syncIds = collect();
        if ($request->has('menu_ids')) {
            $syncIds = $syncIds->merge($request->input('menu_ids'));
        }
        if ($request->filled('decoration_id')) {
            $syncIds->push($request->input('decoration_id'));
        }
        if ($request->filled('serving_type_id')) {
            $syncIds->push($request->input('serving_type_id'));
        }
        if ($request->has('extra_ids')) {
            $syncIds = $syncIds->merge($request->input('extra_ids'));
        }

        // Simpan tanpa quantity tambahan, gunakan default database
        $package->customOptions()->sync($syncIds->toArray());

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(CateringPackage $package)
    {
        $services = CateringService::active()->event()->get();
        $package->load('customOptions');
        return view('admin.packages.edit', compact('package', 'services'));
    }

    public function update(Request $request, CateringPackage $package)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'total_portions' => 'required|integer|min:1',
            'min_addition_qty' => 'nullable|integer|min:0',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'menu_ids' => 'nullable|array',
            'menu_ids.*' => 'exists:custom_options,id',
            'decoration_id' => 'nullable|exists:custom_options,id',
            'serving_type_id' => 'nullable|exists:custom_options,id',
            'extra_ids' => 'nullable|array',
            'extra_ids.*' => 'exists:custom_options,id',
        ]);

        $validated['is_custom'] = $request->boolean('is_custom');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_addition_qty'] = $validated['min_addition_qty'] ?? 0;

        $package->update($validated);

        // Sync pivot custom options
        $syncIds = collect();
        if ($request->has('menu_ids')) {
            $syncIds = $syncIds->merge($request->input('menu_ids'));
        }
        if ($request->filled('decoration_id')) {
            $syncIds->push($request->input('decoration_id'));
        }
        if ($request->filled('serving_type_id')) {
            $syncIds->push($request->input('serving_type_id'));
        }
        if ($request->has('extra_ids')) {
            $syncIds = $syncIds->merge($request->input('extra_ids'));
        }

        $package->customOptions()->sync($syncIds->toArray());

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(CateringPackage $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil dihapus.');
    }
}
