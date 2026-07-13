@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<a href="{{ route('admin.orders') }}" class="text-sm text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali</a>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-2 mb-4">
                <h3 class="text-xl font-bold">{{ $order->order_number }}</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ match($order->status) { 'processing'=>'bg-blue-100 text-blue-700','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-700' } }}">{{ $order->status_label }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Pelanggan:</span><br><b>{{ $order->user->name }}</b><br>{{ $order->user->phone }}<br>{{ $order->user->email }}</div>
                <div><span class="text-gray-500">Layanan:</span><br><b>{{ $order->cateringService->name ?? '-' }}</b></div>
                <div><span class="text-gray-500">Tanggal:</span><br><b>{{ $order->order_date->format('d M Y') }}</b></div>
                <div><span class="text-gray-500">Metode:</span><br><b>{{ ucfirst($order->pickup_method) }}</b></div>
            </div>
            @if($order->pickup_method === 'delivery')
            <div class="mt-4 pt-4 border-t text-sm">
                <b>Alamat:</b> {{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}<br>{{ $order->address_detail }}
                @if($order->latitude && $order->longitude)
                    <p class="text-xs text-gray-400 mt-1">Koordinat: {{ $order->latitude }}, {{ $order->longitude }}</p>
                @endif
            </div>
            @endif
            @if($order->cancellation_reason)
            <div class="mt-4 pt-4 border-t text-sm">
                <p class="text-red-600 font-medium">Alasan Pembatalan:</p>
                <p class="text-gray-700">{{ $order->cancellation_reason }}</p>
            </div>
            @endif
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <h3 class="font-bold mb-4">Item Pesanan</h3>
            <div class="divide-y divide-gray-100">
                @if($order->cateringService?->isDaily())
                    @foreach($order->items as $item)
                    <div class="py-3">
                        <p class="font-medium text-gray-900">{{ $item->formatted_menu_name }}</p>
                        @if($item->formatted_extras)
                            <p class="text-sm text-gray-600 mt-0.5"><span class="font-medium">Extra:</span> {{ $item->formatted_extras }}</p>
                        @endif
                        <p class="text-sm font-medium text-gray-900 mt-1">Total: Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                @else
                    @php
                        $packageItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Paket: '));
                        $menuItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Menu: '));
                        $extraItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Extra: '));
                        $servingItem = $order->items->firstWhere(fn($i) => str_starts_with($i->item_name, 'Penyajian: '));
                        $servingName = $order->serving_type ?? ($servingItem ? preg_replace('/^Penyajian:\s*/i', '', $servingItem->item_name) : null);

                        // Pisahkan item Paket (unit_price == 0) dan item Custom Menu (unit_price > 0)
                        $pkgMenus = $menuItems->where('unit_price', 0);
                        $pkgExtras = $extraItems->where('unit_price', 0);
                        $customMenus = $menuItems->where('unit_price', '>', 0);
                        $customExtras = $extraItems->where('unit_price', '>', 0);
                    @endphp

                    @if($packageItems->isNotEmpty())
                        @foreach($packageItems as $pIdx => $pkg)
                        @php
                            $customPortion = $customMenus->isNotEmpty() ? $customMenus->sum('quantity') : ($customExtras->first()->quantity ?? 0);
                            if ($pkgMenus->isNotEmpty()) {
                                $pkgPortion = $pkgMenus->first()->quantity;
                            } elseif ($customMenus->isNotEmpty() && $order->portion > $customPortion) {
                                $pkgPortion = $order->portion - $customPortion;
                            } else {
                                $pkgPortion = $order->portion ?: ($pkg->quantity * ($order->package->total_portions ?? 1));
                            }
                            $pkgBenefits = $order->package?->benefits ? array_values(array_filter($order->package->benefits, fn($b) => !empty(trim($b)))) : [];
                            $allPkgPelengkap = array_merge($pkgBenefits, $pkgExtras->map(fn($e) => $e->formatted_menu_name)->toArray());
                        @endphp
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-base">{{ $pkg->formatted_menu_name }} <span class="text-gray-600">({{ $pkg->quantity }})</span></h4>
                                    <button type="button" onclick="toggleOrderItemDetail(this, 'admin-detail-pkg-{{ $pIdx }}')" class="mt-1 text-xs font-semibold text-orange-500 hover:text-orange-600 focus:outline-none">Lihat Detail</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900 text-base">Rp {{ number_format($pkg->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div id="admin-detail-pkg-{{ $pIdx }}" class="hidden mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700 space-y-1.5">
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Total Porsi</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $pkgPortion }} Porsi</span>
                                </div>
                                @if($servingName)
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Penyajian</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $servingName }}</span>
                                </div>
                                @endif
                                @if($pkgMenus->isNotEmpty())
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Menu</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $pkgMenus->map(fn($m) => $m->formatted_menu_name)->join(', ') }}</span>
                                </div>
                                @endif
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Pelengkap</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ !empty($allPkgPelengkap) ? implode(', ', $allPkgPelengkap) : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif

                    @if($customMenus->isNotEmpty() || $customExtras->isNotEmpty() || $packageItems->isEmpty())
                        @php
                            $displayCustomMenus = $packageItems->isNotEmpty() ? $customMenus : $menuItems;
                            $displayCustomExtras = $packageItems->isNotEmpty() ? $customExtras : $extraItems;
                            $customPortion = $displayCustomMenus->isNotEmpty() ? $displayCustomMenus->sum('quantity') : ($displayCustomExtras->first()->quantity ?? ($packageItems->isEmpty() ? $order->portion : 0));
                            $customTotal = $packageItems->isNotEmpty() ? ($displayCustomMenus->sum('subtotal') + $displayCustomExtras->sum('subtotal')) : $order->subtotal;
                        @endphp
                        @if($displayCustomMenus->isNotEmpty() || $displayCustomExtras->isNotEmpty() || $packageItems->isEmpty())
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-base">Custom Menu</h4>
                                    <button type="button" onclick="toggleOrderItemDetail(this, 'admin-detail-custom-0')" class="mt-1 text-xs font-semibold text-orange-500 hover:text-orange-600 focus:outline-none">Lihat Detail</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900 text-base">Rp {{ number_format($customTotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div id="admin-detail-custom-0" class="hidden mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700 space-y-1.5">
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Total Porsi</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $customPortion }} Porsi</span>
                                </div>
                                @if($servingName)
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Penyajian</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $servingName }}</span>
                                </div>
                                @endif
                                @if($displayCustomMenus->isNotEmpty())
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Menu</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $displayCustomMenus->map(fn($cm) => $cm->formatted_menu_name . ' (' . $cm->quantity . ')')->join(', ') }}</span>
                                </div>
                                @endif
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Pelengkap</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $displayCustomExtras->isNotEmpty() ? $displayCustomExtras->map(fn($e) => ($e->customOption?->name ?? $e->formatted_menu_name) . ' (' . $e->quantity . ')')->join(', ') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border sticky top-24">
            <h3 class="font-bold mb-4">Pembayaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ number_format($order->subtotal,0,',','.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Ongkir</span><span>Rp {{ number_format($order->shipping_cost,0,',','.') }}</span></div>
                <div class="flex justify-between text-lg font-bold pt-3 border-t"><span>Total</span><span class="text-orange-600">Rp {{ number_format($order->total,0,',','.') }}</span></div>
                <p class="text-xs text-gray-400 mt-2">Pembayaran: Transfer (Midtrans)</p>
                <p class="text-xs text-gray-400">Status: {{ $order->payment_status }}</p>
                @if($order->refund_status && $order->refund_status !== 'none')
                    <p class="text-xs font-medium {{ $order->refund_status === 'pending' ? 'text-yellow-600' : 'text-green-600' }}">
                        Refund: {{ $order->refund_status === 'pending' ? '⏳ Menunggu Refund' : '✅ Sudah Direfund' }}
                    </p>
                @endif
            </div>
            @if(!in_array($order->status, ['completed','cancelled']))
            {{-- Form Update Status --}}
            <form id="statusForm" action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4">
                @csrf @method('PUT')
                <label class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                <select name="status" id="statusSelect" class="w-full px-3 py-2 rounded-lg border text-sm mb-2">
                    @foreach(['processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai'] as $k=>$v)
                    <option value="{{ $k }}" {{ $order->status==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Update Status</button>
            </form>

            {{-- Tombol Batalkan --}}
            <form id="cancelForm" action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="mt-3">
                @csrf @method('PUT')
                <input type="hidden" name="cancellation_reason" id="cancelReasonInput">
                <button type="button" onclick="confirmCancel()" class="w-full px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                    ❌ Batalkan Pesanan
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleOrderItemDetail(btn, targetId) {
    const detailEl = document.getElementById(targetId);
    if (!detailEl) return;

    if (detailEl.classList.contains('hidden')) {
        detailEl.classList.remove('hidden');
        detailEl.style.opacity = '0';
        detailEl.style.transition = 'opacity 0.25s ease-in-out';
        requestAnimationFrame(() => {
            detailEl.style.opacity = '1';
        });
        btn.textContent = 'Sembunyikan Detail';
    } else {
        detailEl.classList.add('hidden');
        btn.textContent = 'Lihat Detail';
    }
}

function confirmCancel() {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Masukkan alasan pembatalan pesanan ini:',
        input: 'textarea',
        inputLabel: 'Alasan Pembatalan',
        inputPlaceholder: 'Tulis alasan pembatalan...',
        inputAttributes: {
            'aria-label': 'Tulis alasan pembatalan',
            maxlength: 500
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        inputValidator: (value) => {
            if (!value || value.trim().length < 5) {
                return 'Alasan pembatalan harus diisi (minimal 5 karakter).';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelReasonInput').value = result.value;
            document.getElementById('cancelForm').submit();
        }
    });
}
</script>
@endpush
@endsection
