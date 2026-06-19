@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 <span class="text-orange-500">Checkout</span></h2>
    <form action="{{ route('customer.checkout.store', ['menu_date' => $menu_date]) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Info -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Detail Pengiriman</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengiriman/Acara *</label>
                            @if($menu_date && $menu_date !== 'unknown')
                                <input type="hidden" name="order_date" value="{{ $orderDate }}">
                                <div class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 font-medium cursor-not-allowed">
                                    {{ \Carbon\Carbon::parse($orderDate)->translatedFormat('l, d F Y') }}
                                </div>
                                <p class="text-xs text-orange-500 mt-1">Tanggal pengiriman mengikuti tanggal menu yang Anda pilih.</p>
                            @else
                                <input type="date" name="order_date" required min="{{ date('Y-m-d') }}" value="{{ old('order_date', $orderDate) }}" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 @error('order_date') border-red-400 @enderror">
                                @error('order_date') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pengambilan *</label>
                            <select name="pickup_method" id="pickup_method" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" onchange="toggleDelivery()">
                                <option value="pickup" {{ old('pickup_method') == 'pickup' ? 'selected' : '' }}>Ambil di Tempat (Pick Up)</option>
                                <option value="delivery" {{ old('pickup_method') == 'delivery' ? 'selected' : '' }}>Delivery (Antar ke Alamat)</option>
                            </select>
                        </div>
                        <div id="delivery-fields" class="{{ old('pickup_method') == 'delivery' ? '' : 'hidden' }} space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                                <select name="district_id" id="district_id" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" onchange="loadVillages(this.value)">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                                <select name="village_id" id="village_id" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400">
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Detail Alamat</label>
                                <textarea name="address_detail" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Nama jalan, nomor rumah, patokan...">{{ old('address_detail') }}</textarea>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                            <select name="payment_method" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400">
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank (Midtrans)</option>
                                <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Bayar di Tempat (COD)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                    <div class="divide-y divide-gray-100 mb-4 max-h-64 overflow-y-auto">
                        @foreach($groupedCarts as $groupKey => $groupItems)
                            @php
                                $isEvent = $groupItems->first()->cart_group_id !== null;
                                $pkgItem = $isEvent ? $groupItems->firstWhere('item_type', 'package') : null;
                            @endphp

                            @if($isEvent)
                                {{-- Event group summary --}}
                                @if($pkgItem && $pkgItem->cateringPackage)
                                <div class="py-3">
                                    <p class="font-medium text-gray-900">📋 {{ $pkgItem->cateringPackage->name }}</p>
                                    <p class="text-xs text-orange-600 font-medium">Rp {{ number_format($pkgItem->cateringPackage->price, 0, ',', '.') }}</p>
                                </div>
                                @endif
                                @foreach($groupItems->where('item_type', '!=', 'package') as $cart)
                                <div class="py-2 flex justify-between text-sm">
                                    <div>
                                        <p class="text-gray-700">{{ $cart->customOption->name ?? 'Item' }}</p>
                                        <p class="text-xs text-gray-500">× {{ $cart->quantity }}
                                            @if($cart->item_type === 'package_item')
                                                <span class="text-green-600">(termasuk)</span>
                                            @endif
                                        </p>
                                    </div>
                                    <p class="font-medium {{ $cart->item_type === 'package_item' ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ $cart->item_type === 'package_item' ? 'Rp 0' : 'Rp ' . number_format($cart->subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                                @endforeach
                            @else
                                {{-- Regular items --}}
                                @foreach($groupItems as $cart)
                                <div class="py-3 flex justify-between text-sm">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $cart->product->name ?? ($cart->customOption->name ?? 'Item') }}</p>
                                        <p class="text-xs text-gray-500">× {{ $cart->quantity }}</p>
                                    </div>
                                    <p class="font-medium text-gray-900">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</p>
                                </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Ongkos Kirim</span>
                            <span class="font-medium" id="shipping-display">Gratis</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-orange-600" id="total-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-4 px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                        Buat Pesanan →
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleDelivery() {
    const method = document.getElementById('pickup_method').value;
    document.getElementById('delivery-fields').classList.toggle('hidden', method !== 'delivery');
    if (method !== 'delivery') {
        document.getElementById('shipping-display').textContent = 'Gratis';
        document.getElementById('total-display').textContent = 'Rp {{ number_format($subtotal, 0, ",", ".") }}';
    }
}

function loadVillages(districtId) {
    if (!districtId) return;
    fetch(`/api/villages/${districtId}`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('village_id');
            sel.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            data.forEach(v => {
                sel.innerHTML += `<option value="${v.id}">${v.name}</option>`;
            });
        });

    fetch(`/api/shipping-cost/${districtId}`)
        .then(r => r.json())
        .then(data => {
            const cost = data.cost;
            document.getElementById('shipping-display').textContent = 'Rp ' + Number(cost).toLocaleString('id-ID');
            const total = {{ $subtotal }} + cost;
            document.getElementById('total-display').textContent = 'Rp ' + Number(total).toLocaleString('id-ID');
        });
}
</script>
@endpush
@endsection
