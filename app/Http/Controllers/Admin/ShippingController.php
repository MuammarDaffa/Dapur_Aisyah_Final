<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\ShippingCost;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $shippingCosts = ShippingCost::with('district')->get();
        $districts = District::all();
        return view('admin.shipping.index', compact('shippingCosts', 'districts'));
    }

    public function create()
    {
        $districts = District::all();
        return view('admin.shipping.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id|unique:shipping_costs,district_id',
            'cost' => 'required|numeric|min:0|max:1000000000',
            'notes' => 'nullable|string|max:255',
        ], [
            'cost.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);
        ShippingCost::create($validated);
        return redirect()->route('admin.shipping.index')->with('success', 'Ongkos kirim berhasil ditambahkan.');
    }

    public function edit(ShippingCost $shipping)
    {
        $districts = District::all();
        return view('admin.shipping.edit', compact('shipping', 'districts'));
    }

    public function update(Request $request, ShippingCost $shipping)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'cost' => 'required|numeric|min:0|max:1000000000',
            'notes' => 'nullable|string|max:255',
        ], [
            'cost.max' => 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
        ]);
        $shipping->update($validated);
        if (!$shipping->wasChanged()) {
            return redirect()->route('admin.shipping.index');
        }
        return redirect()->route('admin.shipping.index')->with('success', 'Ongkos kirim berhasil diperbarui.');
    }

    public function destroy(ShippingCost $shipping)
    {
        $shipping->delete();
        return redirect()->route('admin.shipping.index')->with('success', 'Ongkos kirim berhasil dihapus.');
    }
}
