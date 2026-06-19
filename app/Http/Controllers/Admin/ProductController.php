<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('cateringService');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('service')) {
            $query->where('catering_service_id', $request->service);
        }

        $products = $query->latest()->paginate(15);
        $services = CateringService::all();

        return view('admin.products.index', compact('products', 'services'));
    }

    public function create(Request $request)
    {
        $services = CateringService::active()->get();
        $extras = [];
        if ($request->has('catering_service_id')) {
            $extras = \App\Models\CustomOption::where('catering_service_id', $request->catering_service_id)
                        ->where('type', 'extra')->active()->get();
        }
        return view('admin.products.create', compact('services', 'extras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:1000000000',
            'image' => 'nullable|image|max:2048',
            'is_best_seller' => 'boolean',
            'is_active' => 'boolean',
            'status' => 'required|in:tersedia,habis',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:custom_options,id',
        ], [
            'price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['status'] = $request->status;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        if ($request->has('extras')) {
            $product->extras()->sync($request->extras);
        }

        return redirect()->route('admin.catering.show', $validated['catering_service_id'])
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $services = CateringService::active()->get();
        $extras = \App\Models\CustomOption::where('catering_service_id', $product->catering_service_id)
                        ->where('type', 'extra')->active()->get();
        return view('admin.products.edit', compact('product', 'services', 'extras'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:1000000000',
            'image' => 'nullable|image|max:2048',
            'is_best_seller' => 'boolean',
            'is_active' => 'boolean',
            'status' => 'required|in:tersedia,habis',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:custom_options,id',
        ], [
            'price.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);

        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['status'] = $request->status;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);
        
        $syncResult = $product->extras()->sync($request->extras ?? []);
        $wasSyncChanged = !empty($syncResult['attached']) || !empty($syncResult['detached']) || !empty($syncResult['updated']);

        if (!$product->wasChanged() && !$wasSyncChanged) {
            return redirect()->route('admin.catering.show', $product->catering_service_id);
        }

        return redirect()->route('admin.catering.show', $product->catering_service_id)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $cateringId = $product->catering_service_id;
        $product->delete();
        return redirect()->route('admin.catering.show', $cateringId)
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $products = Product::active()
            ->where('name', 'like', '%' . $request->q . '%')
            ->with('cateringService:id,name')
            ->take(10)
            ->get(['id', 'name', 'price', 'catering_service_id']);

        return response()->json($products);
    }
}
