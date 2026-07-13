@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('customer.orders') }}" class="inline-flex items-center text-sm text-orange-500 hover:text-orange-600 mb-6">← Kembali ke Pesanan</a>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-sm font-medium
                        {{ match($order->status) {
                            'processing' => 'bg-blue-100 text-blue-700',
                            'on_delivery' => 'bg-purple-100 text-purple-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700'
                        } }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Layanan:</span><br><span class="font-medium">{{ $order->cateringService->name ?? '-' }}</span></div>
                    @if(!($order->cateringService?->isDaily()))
                        <div><span class="text-gray-500">Tanggal Acara:</span><br><span class="font-medium">{{ $order->order_date->format('d M Y') }}</span></div>
                    @endif
                    <div><span class="text-gray-500">Metode:</span><br><span class="font-medium">{{ $order->pickup_method === 'delivery' ? 'Delivery' : 'Pick Up' }}</span></div>
                    <div><span class="text-gray-500">Pembayaran:</span><br><span class="font-medium">Transfer</span></div>
                </div>
                @if($order->pickup_method === 'delivery')
                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm">
                        <p class="text-gray-500 mb-1">Alamat Pengiriman:</p>
                        <p class="font-medium">{{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}</p>
                        <p class="text-gray-600">{{ $order->address_detail }}</p>
                    </div>
                @endif
                @if($order->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm">
                        <p class="text-gray-500 mb-1">Catatan:</p>
                        <p>{{ $order->notes }}</p>
                    </div>
                @endif
                @if($order->cancellation_reason)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm">
                        <p class="text-red-600 font-medium mb-1">Alasan Pembatalan:</p>
                        <p>{{ $order->cancellation_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4">Item Pesanan</h3>
                <div class="divide-y divide-gray-200">
                    @if($order->cateringService?->isDaily())
                        {{-- 1. Pesanan Daily Catering --}}
                        @foreach($order->items as $idx => $item)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-base">{{ $item->formatted_menu_name }} <span class="text-gray-600">({{ $item->quantity }})</span></h4>
                                    <button type="button" onclick="toggleOrderItemDetail(this, 'detail-daily-{{ $idx }}')" class="mt-1 text-xs font-semibold text-orange-500 hover:text-orange-600 focus:outline-none">Lihat Detail</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900 text-base">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div id="detail-daily-{{ $idx }}" class="hidden mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700 space-y-1.5">
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Porsi</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $item->quantity }} Porsi</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Harga Satuan</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                </div>
                                @if($item->formatted_extras)
                                <div class="flex items-start">
                                    <span class="w-28 shrink-0 text-gray-500">Pelengkap</span>
                                    <span class="mr-2 text-gray-400">:</span>
                                    <span class="font-medium text-gray-900">{{ $item->formatted_extras }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        {{-- 2. Pesanan Event Catering (Paket & Custom Menu) --}}
                        @php
                            $packageItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Paket: '));
                            $menuItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Menu: '));
                            $extraItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Extra: '));
                            $servingItem = $order->items->firstWhere(fn($i) => str_starts_with($i->item_name, 'Penyajian: '));
                            $servingName = $order->serving_type ?? ($servingItem ? preg_replace('/^Penyajian:\s*/i', '', $servingItem->item_name) : null);
                        @endphp

                        @if($packageItems->isNotEmpty())
                            {{-- Jika pesanan berupa Paket Event --}}
                            @foreach($packageItems as $pIdx => $pkg)
                            @php
                                $pkgMenus = $menuItems;
                                $pkgExtras = $extraItems;
                                $pkgPortion = $order->portion ?: ($pkgMenus->first()->quantity ?? ($pkg->quantity * ($order->package->total_portions ?? 1)));
                            @endphp
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-base">{{ $pkg->formatted_menu_name }} <span class="text-gray-600">({{ $pkg->quantity }})</span></h4>
                                        <button type="button" onclick="toggleOrderItemDetail(this, 'detail-pkg-{{ $pIdx }}')" class="mt-1 text-xs font-semibold text-orange-500 hover:text-orange-600 focus:outline-none">Lihat Detail</button>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900 text-base">Rp {{ number_format($pkg->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div id="detail-pkg-{{ $pIdx }}" class="hidden mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700 space-y-1.5">
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
                                    @if($pkgExtras->isNotEmpty())
                                    <div class="flex items-start">
                                        <span class="w-28 shrink-0 text-gray-500">Pelengkap</span>
                                        <span class="mr-2 text-gray-400">:</span>
                                        <span class="font-medium text-gray-900">{{ $pkgExtras->map(fn($e) => $e->formatted_menu_name . ($e->quantity != $pkgPortion ? ' (' . $e->quantity . ')' : ''))->join(', ') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @endif

                        @if($packageItems->isEmpty() || $menuItems->where('unit_price', '>', 0)->isNotEmpty())
                            @if($packageItems->isEmpty())
                            {{-- Jika pesanan berupa Custom Menu --}}
                            @php
                                $customMenus = $menuItems;
                                $customExtras = $extraItems;
                                $customPortion = $order->portion ?: $customMenus->sum('quantity');
                                $customTotal = $order->subtotal;
                            @endphp
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-base">Custom Menu</h4>
                                        <button type="button" onclick="toggleOrderItemDetail(this, 'detail-custom-0')" class="mt-1 text-xs font-semibold text-orange-500 hover:text-orange-600 focus:outline-none">Lihat Detail</button>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900 text-base">Rp {{ number_format($customTotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div id="detail-custom-0" class="hidden mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700 space-y-1.5">
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
                                    @if($customMenus->isNotEmpty())
                                    <div class="flex items-start">
                                        <span class="w-28 shrink-0 text-gray-500">Menu</span>
                                        <span class="mr-2 text-gray-400">:</span>
                                        <span class="font-medium text-gray-900">{{ $customMenus->map(fn($cm) => $cm->formatted_menu_name . ' (' . $cm->quantity . ')')->join(', ') }}</span>
                                    </div>
                                    @endif
                                    @if($customExtras->isNotEmpty())
                                    <div class="flex items-start">
                                        <span class="w-28 shrink-0 text-gray-500">Pelengkap</span>
                                        <span class="mr-2 text-gray-400">:</span>
                                        <span class="font-medium text-gray-900">{{ $customExtras->map(fn($e) => $e->formatted_menu_name . ($e->quantity != $customPortion ? ' (' . $e->quantity . ')' : ''))->join(', ') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            <!-- Review Form (only for completed orders without review) -->
            @if($order->status === 'completed' && !$order->review)
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">💬 Beri Ulasan</h3>
                    <form action="{{ route('customer.reviews.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
                            <textarea name="comment" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-orange-400" placeholder="Tulis komentar ulasan Anda..." required></textarea>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Kirim Ulasan</button>
                    </form>
                </div>
            @endif

            @if($order->review)
                <div class="bg-orange-50 rounded-xl p-6 border border-orange-100">
                    <h3 class="font-bold text-gray-900 mb-2">💬 Ulasan Anda</h3>
                    <p class="text-sm text-gray-600">{{ $order->review->comment ?? 'Tidak ada komentar.' }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 sticky top-24">
                <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Ongkos Kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-orange-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->payment_status === 'unpaid' && $order->midtrans_snap_token && !in_array($order->status, ['completed', 'cancelled']))
                    <button onclick="payNow()" class="w-full mt-4 px-6 py-3 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-colors">
                        💳 Bayar Sekarang
                    </button>
                @endif

                @if(in_array($order->status, ['pending_payment', 'processing']))
                    <form id="cancelOrderForm" action="{{ route('customer.orders.cancel', $order) }}" method="POST" class="hidden">
                        @csrf @method('PUT')
                        <input type="hidden" name="cancellation_reason" value="Dibatalkan oleh pelanggan">
                    </form>
                    <button type="button" onclick="confirmCancelOrder()" class="w-full mt-3 px-6 py-2.5 border border-red-300 text-red-600 font-medium rounded-xl hover:bg-red-50 transition-colors">
                        Batalkan Pesanan
                    </button>
                @endif

                {{-- Tombol Refund WhatsApp --}}
                @if($order->status === 'cancelled' && ($order->refund_status ?? 'none') === 'pending')
                    @php
                        $waNumber = '6289655951299';
                        $waText = urlencode("Halo Admin Dapur Aisyah,\n\nSaya ingin mengajukan refund untuk pesanan:\n- No. Order: {$order->order_number}\n- Total: Rp " . number_format($order->total, 0, ',', '.') . "\n\nMohon bantuannya. Terima kasih.");
                        $waUrl = "https://wa.me/{$waNumber}?text={$waText}";
                    @endphp
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm text-green-800 font-medium mb-2">Pesanan Anda telah dibatalkan</p>
                        <a href="{{ $waUrl }}" target="_blank" class="block w-full px-6 py-3 bg-green-600 text-white font-semibold text-center rounded-xl hover:bg-green-700 transition-colors">
                            Ajukan Refund via WhatsApp
                        </a>
                    </div>
                @endif

                @if($order->invoice)
                    <p class="mt-4 text-xs text-gray-500 text-center">Invoice: {{ $order->invoice->invoice_number }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media (max-width: 768px) {
    #snap-midtrans {
        width: 100vw !important;
        height: 100vh !important;
        left: 0 !important;
        top: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
    }
}
</style>
@endpush

@push('scripts')
<script>


@if($order->midtrans_snap_token)
function payNow() {
    window.snap.pay('{{ $order->midtrans_snap_token }}', {
        onSuccess: () => location.reload(),
        onPending: () => location.reload(),
        onError: () => alert('Pembayaran gagal'),
        onClose: () => {}
    });
}
@endif

function confirmCancelOrder() {
    Swal.fire({
        title: 'Batalkan Pesanan',
        text: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelOrderForm').submit();
        }
    });
}

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
</script>
@if($order->midtrans_snap_token)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
@endpush
@endsection
