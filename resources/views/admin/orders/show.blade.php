@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<a href="{{ route('admin.orders') }}" class="fs-6 text-primary hover:text-primary mb-4 d-inline-block">← Kembali</a>
<div class="row row-cols-1 lg:row-cols-3 g-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="card shadow-sm mb-4 p-4">
            <div class="d-flex d-flex-column sm:d-flex-row justify-content-between items-start g-3 mb-4">
                <h3 class="fs-4 fw-bold">{{ $order->order_number }}</h3>
                <span class="px-3 py-1 rounded-pill fs-6 fw-medium {{ match($order->status) { 'processing'=>'bg-info text-white text-info','on_delivery'=>'bg-purple-100 text-purple-700','completed'=>'bg-success text-white text-success','cancelled'=>'bg-danger text-white text-danger',default=>'bg-light text-secondary' } }}">{{ $order->status_label }}</span>
            </div>
            <div class="row row-cols-1 sm:row-cols-2 g-3 fs-6">
                <div><span class="text-secondary">Pelanggan:</span><br><b>{{ $order->user->name }}</b><br>{{ $order->user->phone }}<br>{{ $order->user->email }}</div>
                <div><span class="text-secondary">Layanan:</span><br><b>{{ $order->cateringService->name ?? '-' }}</b></div>
                <div><span class="text-secondary">Tanggal:</span><br><b>{{ $order->order_date->format('d M Y') }}</b></div>
                <div><span class="text-secondary">Metode:</span><br><b>{{ ucfirst($order->pickup_method) }}</b></div>
            </div>
            @if($order->pickup_method === 'delivery')
            <div class="mt-4 pt-4 border-t fs-6">
                <b>Alamat:</b> {{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}<br>{{ $order->address_detail }}
                @if($order->latitude && $order->longitude)
                    <p class="small text-secondary mt-1">Koordinat: {{ $order->latitude }}, {{ $order->longitude }}</p>
                @endif
            </div>
            @endif
            @if($order->cancellation_reason)
            <div class="mt-4 pt-4 border-t fs-6">
                <p class="text-danger fw-medium">Alasan Pembatalan:</p>
                <p class="text-secondary">{{ $order->cancellation_reason }}</p>
            </div>
            @endif
        </div>
        <div class="card shadow-sm mb-4 p-4">
            <h3 class="fw-bold mb-4">Item Pesanan</h3>
            <div class="divide-y divide-gray-100">
                @if($order->cateringService?->isDaily())
                    @foreach($order->items as $item)
                    <div class="py-3">
                        <p class="fw-medium text-secondary">{{ $item->formatted_menu_name }}</p>
                        @if($item->formatted_extras)
                            <p class="fs-6 text-secondary mt-0.5"><span class="fw-medium">Extra:</span> {{ $item->formatted_extras }}</p>
                        @endif
                        <p class="fs-6 fw-medium text-secondary mt-1">Total: Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
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
                            <div class="d-flex align-items-center justify-content-between g-3">
                                <div>
                                    <h4 class="fw-bold text-secondary text-base">{{ $pkg->formatted_menu_name }} <span class="text-secondary">({{ $pkg->quantity }})</span></h4>
                                    <button type="button" onclick="toggleOrderItemDetail(this, 'admin-detail-pkg-{{ $pIdx }}')" class="mt-1 small fw-bold text-primary hover:text-primary focus:">Lihat Detail</button>
                                </div>
                                <div class="text-end">
                                    <p class="fw-bold text-secondary text-base">Rp {{ number_format($pkg->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div id="admin-detail-pkg-{{ $pIdx }}" class="d-none mt-3 pt-3 border-t border border-secondary fs-6 text-secondary space-y-1.5">
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
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="d-flex align-items-center justify-content-between g-3">
                                <div>
                                    <h4 class="fw-bold text-secondary text-base">Custom Menu</h4>
                                    <button type="button" onclick="toggleOrderItemDetail(this, 'admin-detail-custom-0')" class="mt-1 small fw-bold text-primary hover:text-primary focus:">Lihat Detail</button>
                                </div>
                                <div class="text-end">
                                    <p class="fw-bold text-secondary text-base">Rp {{ number_format($customTotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div id="admin-detail-custom-0" class="d-none mt-3 pt-3 border-t border border-secondary fs-6 text-secondary space-y-1.5">
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
    </div>
    <div class="space-y-6">
        <div class="card shadow-sm mb-4 p-4">
            <h3 class="fw-bold mb-4">Pembayaran</h3>
            <div class="d-flex flex-column gap-2 fs-6">
                <div class="d-flex justify-content-between"><span class="text-secondary">Subtotal</span><span>Rp {{ number_format($order->subtotal,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-secondary">Ongkir</span><span>Rp {{ number_format($order->shipping_cost,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between fs-5 fw-bold pt-3 border-t"><span>Total</span><span class="text-primary">Rp {{ number_format($order->total,0,',','.') }}</span></div>
                <p class="small text-secondary mt-2">Pembayaran: Transfer (Midtrans)</p>
                <p class="small text-secondary">Status: {{ $order->payment_status }}</p>
                @if($order->refund_status && $order->refund_status !== 'none')
                    <p class="small fw-medium d-inline-d-flex align-items-center g-3 {{ $order->refund_status === 'pending' ? 'text-warning' : 'text-success' }}">
                        @if($order->refund_status === 'pending')
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Refund: Menunggu Refund</span>
                        @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Refund: Sudah Direfund</span>
                        @endif
                    </p>
                @endif
            </div>
            @if(!in_array($order->status, ['completed','cancelled']))
            {{-- Form Update Status --}}
            <form id="statusForm" action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4">
                @csrf @method('PUT')
                <label class="form-label fw-bold">Update Status</label>
                <select name="status" id="statusSelect" class="form-select w-100 px-3 py-2 rounded border fs-6 mb-2">
                    @foreach(['processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai'] as $k=>$v)
                    <option value="{{ $k }}" {{ $order->status==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-100 px-4 py-2 bg-info text-white text-white fs-6 rounded hover:bg-info text-white">Update Status</button>
            </form>

            {{-- Tombol Batalkan --}}
            <form id="cancelForm" action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="mt-3">
                @csrf @method('PUT')
                <input type="hidden" name="cancellation_reason" id="cancelReasonInput">
                <button type="button" onclick="confirmCancel()" class="btn btn-danger">
                    <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Batalkan Pesanan</span>
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
