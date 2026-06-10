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

    public function create()
    {
        $services = CateringService::active()->get();
        return view('admin.products.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_best_seller' => 'boolean',
            'available_days' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $services = CateringService::active()->get();
        return view('admin.products.edit', compact('product', 'services'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'catering_service_id' => 'required|exists:catering_services,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_best_seller' => 'boolean',
            'available_days' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
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
