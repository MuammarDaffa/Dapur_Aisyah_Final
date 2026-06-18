@extends('layouts.app')
@section('title', 'Detail Paket - ' . $package->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('customer.event.service', $service->id) }}" class="text-sm text-orange-500 hover:text-orange-600">← Kembali ke Layanan</a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $package->name }}</h2>
        <p class="text-gray-500">{{ $package->total_portions }} Porsi</p>
    </div>

    <form action="{{ route('customer.event.cart.store') }}" method="POST" onkeydown="return event.key != 'Enter';">
        @csrf
        <input type="hidden" name="catering_service_id" value="{{ $service->id }}">
        <input type="hidden" name="catering_package_id" value="{{ $package->id }}">

        @php
            $menus = $package->customOptions->where('type', 'menu')->values();
            $extras = $package->customOptions->where('type', 'extra')->values();
            $servings = $package->customOptions->where('type', 'serving_type')->values();
        @endphp

        <div class="space-y-6">
            {{-- Menu Paket: bagi porsi --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Bagi Porsi ke Menu</h3>
                <p class="text-sm text-gray-500 mb-4">Total porsi menu harus tepat sama dengan porsi paket.</p>

                {{-- Porsi Indicator --}}
                <div class="mb-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50" id="pkg-portion-indicator">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-gray-700">Target Porsi: <span class="text-orange-600">{{ $package->total_portions }}</span></span>
                        <span class="text-sm font-semibold text-gray-700">Total Dipilih: <span id="pkg-selected" class="text-blue-600">0</span></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div id="pkg-progress-bar" class="h-3 rounded-full transition-all duration-300 bg-yellow-500" style="width:0%"></div>
                    </div>
                    <p id="pkg-portion-msg" class="text-sm mt-2 font-medium text-yellow-700">⚠️ Kurang {{ $package->total_portions }} porsi lagi.</p>
                </div>

                <div class="space-y-3">
                    @foreach($menus as $idx => $menu)
                        <div class="flex items-center justify-between p-4 border rounded-xl bg-white">
                            <div>
                                <p class="font-medium text-gray-900">{{ $menu->name }}</p>
                                <p class="text-xs text-green-600 font-medium">Termasuk dalam paket</p>
                                <input type="hidden" name="items[{{ $idx }}][custom_option_id]" value="{{ $menu->id }}">
                                <input type="hidden" name="items[{{ $idx }}][item_type]" value="package_item">
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="changePkgQty({{ $idx }}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                                <input type="number" name="items[{{ $idx }}][quantity]" id="pkg_qty_{{ $idx }}" value="0" min="0" max="{{ $package->total_portions }}"
                                    class="w-16 text-center border rounded-lg py-1 font-semibold pkg-qty-input" onchange="recalcPkgPortions()">
                                <button type="button" onclick="changePkgQty({{ $idx }}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Free Items --}}
            @if($extras->isNotEmpty())
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Free (Termasuk Paket)</h3>
                    <div class="space-y-2">
                        @foreach($extras as $idx => $extra)
                            <div class="flex items-center gap-2 p-3 bg-green-50 rounded-lg border border-green-100">
                                <span class="text-green-600 font-bold">✓</span>
                                <span class="text-gray-900 font-medium">{{ $extra->name }}</span>
                                <span class="text-green-600 text-sm font-medium ml-auto">FREE</span>
                                <input type="hidden" name="items[{{ $menus->count() + $idx }}][custom_option_id]" value="{{ $extra->id }}">
                                <input type="hidden" name="items[{{ $menus->count() + $idx }}][item_type]" value="addition">
                                <input type="hidden" name="items[{{ $menus->count() + $idx }}][quantity]" value="{{ $package->total_portions }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Penyajian Paket --}}
            @if($servings->isNotEmpty())
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Penyajian</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($servings as $idx => $serving)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="serving_type_id" value="{{ $serving->id }}" class="peer sr-only" {{ $idx === 0 ? 'checked' : '' }}>
                                <div class="p-4 text-center border-2 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-colors">
                                    <span class="font-medium text-gray-900">{{ $serving->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Ringkasan & Tombol --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="p-4 bg-orange-50 rounded-xl border border-orange-100 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total Harga Paket</span>
                        <span class="text-2xl font-bold text-orange-600">{{ $package->formatted_price }}</span>
                    </div>
                </div>
                <button type="submit" id="pkg-submit-btn" disabled
                    class="w-full px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-lg rounded-xl hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    Masukkan ke Keranjang 🛒
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const targetPortions = {{ $package->total_portions }};

    function changePkgQty(idx, delta) {
        const input = document.getElementById('pkg_qty_' + idx);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        input.value = val;
        recalcPkgPortions();
    }

    function recalcPkgPortions() {
        let total = 0;
        document.querySelectorAll('.pkg-qty-input').forEach(input => {
            total += parseInt(input.value) || 0;
        });

        document.getElementById('pkg-selected').textContent = total;

        const pct = Math.min(100, (total / targetPortions) * 100);
        const bar = document.getElementById('pkg-progress-bar');
        bar.style.width = pct + '%';

        const indicator = document.getElementById('pkg-portion-indicator');
        const msg = document.getElementById('pkg-portion-msg');
        const btn = document.getElementById('pkg-submit-btn');

        if (total < targetPortions) {
            bar.className = 'h-3 rounded-full transition-all duration-300 bg-yellow-500';
            indicator.className = 'mb-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50';
            msg.textContent = `⚠️ Kurang ${targetPortions - total} porsi lagi.`;
            msg.className = 'text-sm mt-2 font-medium text-yellow-700';
            btn.disabled = true;
        } else if (total > targetPortions) {
            bar.className = 'h-3 rounded-full transition-all duration-300 bg-red-500';
            indicator.className = 'mb-4 p-4 rounded-xl border border-red-200 bg-red-50';
            msg.textContent = `❌ Kelebihan ${total - targetPortions} porsi. Kurangi porsi.`;
            msg.className = 'text-sm mt-2 font-medium text-red-700';
            btn.disabled = true;
        } else {
            bar.className = 'h-3 rounded-full transition-all duration-300 bg-green-500';
            indicator.className = 'mb-4 p-4 rounded-xl border border-green-200 bg-green-50';
            msg.textContent = `✅ Porsi sudah tepat! Siap dimasukkan ke keranjang.`;
            msg.className = 'text-sm mt-2 font-medium text-green-700';
            btn.disabled = false;
        }
    }
</script>
@endpush
@endsection
