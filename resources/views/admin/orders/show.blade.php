@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.orders') }}" class="btn btn-default"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Invoice Layout -->
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Invoice: {{ $order->order_number }}</h3>
                <div class="ms-auto">
                    <span class="badge {{ match($order->status) { 'processing'=>'text-bg-info','on_delivery'=>'text-bg-primary','completed'=>'text-bg-success','cancelled'=>'text-bg-danger',default=>'text-bg-secondary' } }}">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row invoice-info mb-4">
                    <div class="col-sm-4 invoice-col">
                        Pelanggan
                        <address>
                            <strong>{{ $order->user->name }}</strong><br>
                            Telepon: {{ $order->user->phone }}<br>
                            Email: {{ $order->user->email }}
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        Detail Pesanan
                        <address>
                            <strong>{{ $order->cateringService->name ?? '-' }}</strong><br>
                            Tanggal: {{ $order->order_date->format('d M Y') }}<br>
                            Metode: {{ ucfirst($order->pickup_method) }}
                        </address>
                    </div>
                    @if($order->pickup_method === 'delivery')
                    <div class="col-sm-4 invoice-col">
                        Alamat Pengiriman
                        <address>
                            <strong>{{ $order->district->name ?? '' }}, {{ $order->village->name ?? '' }}</strong><br>
                            {{ $order->address_detail }}<br>
                            @if($order->latitude && $order->longitude)
                                Koordinat: {{ $order->latitude }}, {{ $order->longitude }}
                            @endif
                        </address>
                    </div>
                    @endif
                </div>

                @if($order->cancellation_reason)
                <div class="callout callout-danger mb-4">
                    <h5><i class="fa-solid fa-ban text-danger"></i> Alasan Pembatalan:</h5>
                    <p>{{ $order->cancellation_reason }}</p>
                </div>
                @endif

                <h4 class="mb-3">Item Pesanan</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Detail</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($order->cateringService?->isDaily())
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="align-middle fw-medium">{{ $item->formatted_menu_name }}</td>
                                    <td class="align-middle">
                                        @if($item->formatted_extras)
                                            <small>Extra: {{ $item->formatted_extras }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @else
                                @php
                                    $packageItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Paket: '));
                                    $menuItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Menu: '));
                                    $extraItems = $order->items->filter(fn($i) => str_starts_with($i->item_name, 'Extra: '));
                                    $servingItem = $order->items->firstWhere(fn($i) => str_starts_with($i->item_name, 'Penyajian: '));
                                    $servingName = $order->serving_type ?? ($servingItem ? preg_replace('/^Penyajian:\s*/i', '', $servingItem->item_name) : null);

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
                                    <tr>
                                        <td class="align-middle">
                                            <span class="fw-bold">{{ $pkg->formatted_menu_name }} ({{ $pkg->quantity }})</span>
                                        </td>
                                        <td class="align-middle">
                                            <ul class="list-unstyled mb-0 small text-muted">
                                                <li><strong>Porsi:</strong> {{ $pkgPortion }}</li>
                                                @if($servingName)<li><strong>Penyajian:</strong> {{ $servingName }}</li>@endif
                                                @if($pkgMenus->isNotEmpty())<li><strong>Menu:</strong> {{ $pkgMenus->map(fn($m) => $m->formatted_menu_name)->join(', ') }}</li>@endif
                                                <li><strong>Pelengkap:</strong> {{ !empty($allPkgPelengkap) ? implode(', ', $allPkgPelengkap) : '-' }}</li>
                                            </ul>
                                        </td>
                                        <td class="align-middle text-end fw-bold">Rp {{ number_format($pkg->subtotal, 0, ',', '.') }}</td>
                                    </tr>
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
                                    <tr>
                                        <td class="align-middle fw-bold">Custom Menu</td>
                                        <td class="align-middle">
                                            <ul class="list-unstyled mb-0 small text-muted">
                                                <li><strong>Porsi:</strong> {{ $customPortion }}</li>
                                                @if($servingName)<li><strong>Penyajian:</strong> {{ $servingName }}</li>@endif
                                                @if($displayCustomMenus->isNotEmpty())<li><strong>Menu:</strong> {{ $displayCustomMenus->map(fn($cm) => $cm->formatted_menu_name . ' (' . $cm->quantity . ')')->join(', ') }}</li>@endif
                                                <li><strong>Pelengkap:</strong> {{ $displayCustomExtras->isNotEmpty() ? $displayCustomExtras->map(fn($e) => ($e->customOption?->name ?? $e->formatted_menu_name) . ' (' . $e->quantity . ')')->join(', ') : '-' }}</li>
                                            </ul>
                                        </td>
                                        <td class="align-middle text-end fw-bold">Rp {{ number_format($customTotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-success mb-4">
            <div class="card-header">
                <h3 class="card-title">Pembayaran</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal,0,',','.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Ongkos Kirim</span>
                    <span>Rp {{ number_format($order->shipping_cost,0,',','.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong class="fs-5">Total</strong>
                    <strong class="fs-5 text-success">Rp {{ number_format($order->total,0,',','.') }}</strong>
                </div>
                <p class="small text-muted mb-1">Metode: Transfer (Midtrans)</p>
                <p class="small text-muted mb-1">Status: {{ $order->payment_status }}</p>
                
                @if($order->refund_status && $order->refund_status !== 'none')
                    <div class="alert alert-{{ $order->refund_status === 'pending' ? 'warning' : 'success' }} py-2 mt-3 mb-0">
                        <i class="fa-solid fa-{{ $order->refund_status === 'pending' ? 'clock' : 'check' }}"></i>
                        Refund: {{ $order->refund_status === 'pending' ? 'Menunggu Refund' : 'Sudah Direfund' }}
                    </div>
                @endif
            </div>
        </div>

        @if(!in_array($order->status, ['completed','cancelled']))
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Aksi Pesanan</h3>
            </div>
            <div class="card-body">
                {{-- Form Update Status --}}
                <form id="statusForm" action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Update Status</label>
                        <select name="status" class="form-select">
                            @foreach(['processing'=>'Diproses','on_delivery'=>'Dikirim','completed'=>'Selesai'] as $k=>$v)
                            <option value="{{ $k }}" {{ $order->status==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3"><i class="fa-solid fa-sync"></i> Update Status</button>
                </form>

                {{-- Tombol Batalkan --}}
                <form id="cancelForm" action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="cancellation_reason" id="cancelReasonInput">
                    <button type="button" onclick="confirmCancel()" class="btn btn-outline-danger w-100">
                        <i class="fa-solid fa-times"></i> Batalkan Pesanan
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
function confirmCancel() {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Masukkan alasan pembatalan pesanan ini:',
        input: 'textarea',
        inputPlaceholder: 'Tulis alasan pembatalan...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
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
