<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringService;
use App\Models\CustomOption;
use Illuminate\Http\Request;

class CustomOptionController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomOption::with('cateringService');
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('service')) {
            $query->where('catering_service_id', $request->service);
        }
        $options = $query->latest()->paginate(15);
        $services = CateringService::active()->get();

        // Kumpulkan semua tipe unik yang ada di database
        $types = CustomOption::select('type')->distinct()->pluck('type')->toArray();

        return view('admin.custom-options.index', compact('options', 'services', 'types'));
    }

    public function create()
    {
        $services = CateringService::active()->get();
        $types = CustomOption::select('type')->distinct()->pluck('type')->toArray();
        return view('admin.custom-options.create', compact('services', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'type' => 'required|in:menu,decoration,serving_type,extra',
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'min_qty' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_qty'] = $validated['min_qty'] ?? 0;
        CustomOption::create($validated);
        return redirect()->route('admin.custom-options.index')->with('success', 'Opsi custom berhasil ditambahkan.');
    }

    public function edit(CustomOption $customOption)
    {
        $services = CateringService::active()->get();
        $types = CustomOption::select('type')->distinct()->pluck('type')->toArray();
        return view('admin.custom-options.edit', compact('customOption', 'services', 'types'));
    }

    public function update(Request $request, CustomOption $customOption)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'type' => 'required|in:menu,decoration,serving_type,extra',
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'min_qty' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_qty'] = $validated['min_qty'] ?? 0;
        $customOption->update($validated);
        return redirect()->route('admin.custom-options.index')->with('success', 'Opsi custom berhasil diperbarui.');
    }

    public function destroy(CustomOption $customOption)
    {
        $customOption->delete();
        return redirect()->route('admin.custom-options.index')->with('success', 'Opsi custom berhasil dihapus.');
    }
}
