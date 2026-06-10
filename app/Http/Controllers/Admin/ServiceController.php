<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = CateringService::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'serving_types' => 'nullable|array',
            'min_portion' => 'required|integer|min:1',
            'max_portion' => 'nullable|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'service_area' => 'nullable|array',
            'available_features' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['available_features'] = $request->input('available_features', []);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        CateringService::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(CateringService $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, CateringService $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'serving_types' => 'nullable|array',
            'min_portion' => 'required|integer|min:1',
            'max_portion' => 'nullable|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'order_terms' => 'nullable|string',
            'schedule_notes' => 'nullable|string',
            'service_area' => 'nullable|array',
            'available_features' => 'nullable|array',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['available_features'] = $request->input('available_features', []);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(CateringService $service)
    {
        try {
            // Hapus paksa pesanan yang terkait agar tidak terjadi error foreign key
            // (Tabel terkait pesanan seperti invoices, reviews, order_items sudah memiliki cascadeOnDelete)
            \App\Models\Order::where('catering_service_id', $service->id)->delete();

            $service->delete();
            return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus beserta data yang terkait.');
        } catch (\Exception $e) {
            return redirect()->route('admin.services.index')->with('error', 'Gagal menghapus layanan: ' . $e->getMessage());
        }
    }
}
