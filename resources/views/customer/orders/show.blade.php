@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('customer.orders') }}" class="d-inline-d-flex align-items-center fs-6 text-primary hover:text-primary mb-6">
        <svg style="width: 16px; height: 16px;" class="me-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        <span>Kembali ke Pesanan</span>
    </a>


    <div class="row row-cols-1 lg:row-cols-3 g-3">
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="card shadow-sm mb-4 p-4">
                <div class="d-flex justify-content-between items-start mb-4">
                    <div>
                        <h2 class="fs-4 fw-bold text-secondary">{{ $order->order_number }}</h2>
                        <p class="fs-6 text-secondary">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-pill fs-6 fw-medium {{ match($order->status) { 'processing' => 'bg-info text-white text-info', 'on_delivery' => 'bg-purple-100 text-purple-700', 'completed' => 'bg-success text-white text-success', 'cancelled' => 'bg-danger text-white text-danger', default => 'bg-light text-secondary' } }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="row row-cols-2 g-3 fs-6">
                    <div><span class="text-secondary">Layanan:</span><br><span class="fw-medium">{{ $order->cateringService->name ?? '-' }}</span></div>
                    @if(!($order->cateringService?->isDaily()))
                        <div><span class="text-secondary">Tanggal Acara:</span><br><span class="fw-medium">{{ $order->order_date->format('d M Y') }}</span></div>
                    @endif
                    <div><span class="text-secondary">Metode:</span><br><span class="fw-medium">{{ $order->pickup_method === 'delivery' ? 'Delivery' : 'Pick Up' }}</span></div>
                    <div><span class="text-secondary">Pembayaran:</span><br><span class="fw-medium">Transfer</span></div>
                </div>
                @if($order->pickup_method === 'delivery')
                    <div class="mt-4 pt-4 border-t border border-secondary fs-6">
                        <p class="text-secondary mb-1">Alamat Pengiriman:</p>
                        <p class="fw-medium">{{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}</p>
                        <p class="text-secondary">{{ $order->address_detail }}</p>
                    </div>
                @endif
                @if($order->notes)
                    <div class="mt-4 pt-4 border-t border border-secondary fs-6">
                        <p class="text-secondary mb-1">Catatan:</p>
                        <p>{{ $order->notes }}</p>
                    </div>
                @endif
                @if($order->cancellation_reason)
                    <div class="mt-4 pt-4 border-t border border-secondary fs-6">
                        <p class="text-danger fw-medium mb-1">Alasan Pembatalan:</p>
                        <p>{{ $order->cancellation_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="card shadow-sm mb-4 p-4">
                <h3 class="fw-bold text-secondary mb-4">Item Pesanan</h3>
                <div class="divide-y divide-gray-200">
                    @if($order->cateringService?->isDaily())
                        {{-- 1. Pesanan Daily Catering --}}
                        @foreach($order->items as $idx => $item)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="d-flex align-items-center justify-content-between g-3">
                                <div>
                                    <h4 class="fw-bold text-secondary text-base">{{ $item->formatted_menu_name }} <span class="text-secondary">({{ $item->quantity }})</span></h4>
                                    <button type="button" onclick="toggleOrderItemDetail(this, 'detail-daily-{{ $idx }}')" class="mt-1 small fw-bold text-primary hover:text-primary focus:">Lihat Detail</button>
                                </div>
                                <div class="text-end">
                                    <p class="fw-bold text-secondary text-base">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div id="detail-daily-{{ $idx }}" class="d-none mt-3 pt-3 border-t border border-secondary fs-6 text-secondary space-y-1.5">
                                <div class="d-flex items-start">
                                    <span class="w-28 flex-shrink-0 text-secondary">Porsi</span>
                                    <span class="me-2 text-secondary">:</span>
                                    <span class="fw-medium text-secondary">{{ $item->quantity }} Porsi</span>
                                </div>
                                <div class="d-flex items-start">
                                    <span class="w-28 flex-shrink-0 text-secondary">Harga Satuan</span>
                                    <span class="me-2 text-secondary">:</span>
                                    <span class="fw-medium text-secondary">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                </div>
                                @if($item->formatted_extras)
                                <div class="d-flex items-start">
                                    <span class="w-28 flex-shrink-0 text-secondary">Tambahan</span>
                                    <span class="me-2 text-secondary">:</span>
                                    <span class="fw-medium text-secondary">{{ $item->formatted_extras }}</span>
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

                            // Pisahkan item Paket (unit_price == 0) dan item Custom Menu (unit_price > 0)
                            $pkgMenus = $menuItems->where('unit_price', 0);
                            $pkgExtras = $extraItems->where('unit_price', 0);
                            $customMenus = $menuItems->where('unit_price', '>', 0);
                            $customExtras = $extraItems->where('unit_price', '>', 0);
                        @endphp

                        @if($packageItems->isNotEmpty())
                            {{-- Jika pesanan berupa Paket Event --}}
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
                                <div class="d-flex align-items-center justify-content-between g-3">
                                    <div>
                                        <h4 class="fw-bold text-secondary text-base">{{ $pkg->formatted_menu_name }} <span class="text-secondary">({{ $pkg->quantity }})</span></h4>
                                        <button type="button" onclick="toggleOrderItemDetail(this, 'detail-pkg-{{ $pIdx }}')" class="mt-1 small fw-bold text-primary hover:text-primary focus:">Lihat Detail</button>
                                    </div>
                                    <div class="text-end">
                                        <p class="fw-bold text-secondary text-base">Rp {{ number_format($pkg->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div id="detail-pkg-{{ $pIdx }}" class="d-none mt-3 pt-3 border-t border border-secondary fs-6 text-secondary space-y-1.5">
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Total Porsi</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ $pkgPortion }} Porsi</span>
                                    </div>
                                    @if($servingName)
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Penyajian</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ $servingName }}</span>
                                    </div>
                                    @endif
                                    @if($pkgMenus->isNotEmpty())
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Menu</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ $pkgMenus->map(fn($m) => $m->formatted_menu_name)->join(', ') }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Pelengkap</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ !empty($allPkgPelengkap) ? implode(', ', $allPkgPelengkap) : '-' }}</span>
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
                            {{-- Jika pesanan berupa Custom Menu --}}
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="d-flex align-items-center justify-content-between g-3">
                                    <div>
                                        <h4 class="fw-bold text-secondary text-base">Custom Menu</h4>
                                        <button type="button" onclick="toggleOrderItemDetail(this, 'detail-custom-0')" class="mt-1 small fw-bold text-primary hover:text-primary focus:">Lihat Detail</button>
                                    </div>
                                    <div class="text-end">
                                        <p class="fw-bold text-secondary text-base">Rp {{ number_format($customTotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div id="detail-custom-0" class="d-none mt-3 pt-3 border-t border border-secondary fs-6 text-secondary space-y-1.5">
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Total Porsi</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ $customPortion }} Porsi</span>
                                    </div>
                                    @if($servingName)
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Penyajian</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ $servingName }}</span>
                                    </div>
                                    @endif
                                    @if($displayCustomMenus->isNotEmpty())
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Menu</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ $displayCustomMenus->map(fn($cm) => $cm->formatted_menu_name . ' (' . $cm->quantity . ')')->join(', ') }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex items-start">
                                        <span class="w-28 flex-shrink-0 text-secondary">Pelengkap</span>
                                        <span class="me-2 text-secondary">:</span>
                                        <span class="fw-medium text-secondary">{{ $displayCustomExtras->isNotEmpty() ? $displayCustomExtras->map(fn($e) => ($e->customOption?->name ?? $e->formatted_menu_name) . ' (' . $e->quantity . ')')->join(', ') : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            <!-- Review Form (only for completed orders without review) -->
            @if($order->status === 'completed' && !$order->review)
                <div class="card shadow-sm mb-4 p-4">
                    <h3 class="fw-bold text-secondary mb-4 d-flex align-items-center">
                        <svg style="width: 20px; height: 20px;" class="text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        <span>Beri Ulasan</span>
                    </h3>
                    <form action="{{ route('customer.reviews.store') }}" method="POST" class="d-flex flex-column gap-3">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="mb-3">
            <label class="form-label fw-bold">Komentar</label>
                            <textarea name="comment" rows="3" class="form-control w-100 px-4 py-2 rounded border border border-secondary focus:border border-primary" placeholder="Tulis komentar ulasan Anda..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                    </form>
                </div>
            @endif

            @if($order->review)
                <div class="bg-primary text-white rounded p-6 border border border-primary">
                    <h3 class="fw-bold text-secondary mb-2 d-flex align-items-center">
                        <svg style="width: 20px; height: 20px;" class="text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        <span>Ulasan Anda</span>
                    </h3>
                    <p class="fs-6 text-secondary">{{ $order->review->comment ?? 'Tidak ada komentar.' }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="card shadow-sm mb-4 p-4">
                <h3 class="fw-bold text-secondary mb-4">Ringkasan Pembayaran</h3>
                <div class="d-flex flex-column gap-2 fs-6">
                    <div class="d-flex justify-content-between"><span class="text-secondary">Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-secondary">Ongkos Kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between fs-5 fw-bold pt-3 border-t border border-secondary">
                        <span>Total</span>
                        <span class="text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->payment_status === 'unpaid' && $order->midtrans_snap_token && !in_array($order->status, ['completed', 'cancelled']))
                    <button onclick="payNow()" class="w-100 mt-4 px-6 py-3 bg-success text-white text-white fw-bold rounded hover:bg-success text-white d-inline-d-flex align-items-center justify-content-center g-3">
                        <svg style="width: 20px; height: 20px;" class="flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span>Bayar Sekarang</span>
                    </button>
                @endif

                @if(in_array($order->status, ['pending_payment', 'processing']))
                    <form id="cancelOrderForm" action="{{ route('customer.orders.cancel', $order) }}" method="POST" class="d-none">
                        @csrf @method('PUT')
                        <input type="hidden" name="cancellation_reason" value="Dibatalkan oleh pelanggan">
                    </form>
                    <button type="button" onclick="confirmCancelOrder()" class="btn btn-outline-danger btn btn-danger w-100 mt-3 px-6 py-2.5 border border-red-300 text-danger fw-medium rounded hover:bg-danger text-white">
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
                    <div class="mt-4 p-4 bg-success text-white border border-green-200 rounded">
                        <p class="fs-6 text-success fw-medium mb-2">Pesanan Anda telah dibatalkan</p>
                        <a href="{{ $waUrl }}" target="_blank" class="d-block w-100 px-6 py-3 bg-success text-white text-white fw-bold text-center rounded hover:bg-success text-white">
                            Ajukan Refund via WhatsApp
                        </a>
                    </div>
                @endif

                @if($order->invoice)
                    <p class="mt-4 small text-secondary text-center">Invoice: {{ $order->invoice->invoice_number }}</p>
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
