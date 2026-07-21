@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.pesanan') }}" class="btn btn-default"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Tagihan Layout -->
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Tagihan: {{ $pesanan->nomor_pesanan }}</h3>
                <div class="ms-auto">
                    <span class="badge {{ match($pesanan->status) { 'diproses'=>'text-bg-info','dikirim'=>'text-bg-primary','selesai'=>'text-bg-success','dibatalkan'=>'text-bg-danger',default=>'text-bg-secondary' } }}">
                        {{ $pesanan->status_label }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row tagihan-info mb-4">
                    <div class="col-sm-4 tagihan-col">
                        Pelanggan
                        <address>
                            <strong>{{ $pesanan->user->name }}</strong><br>
                            Telepon: {{ $pesanan->user->phone }}<br>
                            Email: {{ $pesanan->user->email }}
                        </address>
                    </div>
                    <div class="col-sm-4 tagihan-col">
                        Detail Pesanan
                        <address>
                            <strong>{{ $pesanan->layananKatering->name ?? '-' }}</strong><br>
                            Tanggal: {{ $pesanan->tanggal_pesanan->format('d M Y') }}<br>
                            Metode: {{ ucfirst($pesanan->metode_pengambilan) }}
                        </address>
                    </div>
                    @if($pesanan->metode_pengambilan === 'delivery')
                    <div class="col-sm-4 tagihan-col">
                        Alamat Pengiriman
                        <address>

                            {{ $pesanan->detail_alamat }}<br>
                            @if($pesanan->latitude && $pesanan->longitude)
                                Koordinat: {{ $pesanan->latitude }}, {{ $pesanan->longitude }}
                            @endif
                        </address>
                    </div>
                    @endif
                </div>

                @if($pesanan->alasan_pembatalan)
                <div class="callout callout-danger mb-4">
                    <h5><i class="fa-solid fa-ban text-danger"></i> Alasan Pembatalan:</h5>
                    <p>{{ $pesanan->alasan_pembatalan }}</p>
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
                            @if($pesanan->layananKatering?->isHarian())
                                @foreach($pesanan->items as $item)
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
                                    $packageItems = $pesanan->items->filter(fn($i) => str_starts_with($i->item_name, 'Paket: '));
                                    $menuItems = $pesanan->items->filter(fn($i) => str_starts_with($i->item_name, 'Menu: '));
                                    $extraItems = $pesanan->items->filter(fn($i) => str_starts_with($i->item_name, 'Extra: '));
                                    $servingItem = $pesanan->items->firstWhere(fn($i) => str_starts_with($i->item_name, 'Penyajian: '));
                                    $servingName = $pesanan->tipe_penyajian ?? ($servingItem ? preg_replace('/^Penyajian:\s*/i', '', $servingItem->item_name) : null);

                                    $pkgMenus = $menuItems->where('unit_price', 0);
                                    $pkgExtras = $extraItems->where('unit_price', 0);
                                    $customMenus = $menuItems->where('unit_price', '>', 0);
                                    $customExtras = $extraItems->where('unit_price', '>', 0);
                                @endphp

                                @if($packageItems->isNotEmpty())
                                    @foreach($packageItems as $pIdx => $pkg)
                                    @php
                                        $customPortion = $customMenus->isNotEmpty() ? $customMenus->sum('jumlah') : ($customExtras->first()->jumlah ?? 0);
                                        if ($pkgMenus->isNotEmpty()) {
                                            $pkgPortion = $pkgMenus->first()->jumlah;
                                        } elseif ($customMenus->isNotEmpty() && $pesanan->porsi > $customPortion) {
                                            $pkgPortion = $pesanan->porsi - $customPortion;
                                        } else {
                                            $pkgPortion = $pesanan->porsi ?: ($pkg->jumlah * ($pesanan->package->total_portions ?? 1));
                                        }
                                        $pkgBenefits = $pesanan->package?->benefits ? array_values(array_filter($pesanan->package->benefits, fn($b) => !empty(trim($b)))) : [];
                                        $allPkgPelengkap = array_merge($pkgBenefits, $pkgExtras->map(fn($e) => $e->formatted_menu_name)->toArray());
                                    @endphp
                                    <tr>
                                        <td class="align-middle">
                                            <span class="fw-bold">{{ $pkg->formatted_menu_name }} ({{ $pkg->jumlah }})</span>
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
                                        $customPortion = $displayCustomMenus->isNotEmpty() ? $displayCustomMenus->sum('jumlah') : ($displayCustomExtras->first()->jumlah ?? ($packageItems->isEmpty() ? $pesanan->porsi : 0));
                                        $customTotal = $packageItems->isNotEmpty() ? ($displayCustomMenus->sum('subtotal') + $displayCustomExtras->sum('subtotal')) : $pesanan->subtotal;
                                    @endphp
                                    @if($displayCustomMenus->isNotEmpty() || $displayCustomExtras->isNotEmpty() || $packageItems->isEmpty())
                                    <tr>
                                        <td class="align-middle fw-bold">Custom Menu</td>
                                        <td class="align-middle">
                                            <ul class="list-unstyled mb-0 small text-muted">
                                                <li><strong>Porsi:</strong> {{ $customPortion }}</li>
                                                @if($servingName)<li><strong>Penyajian:</strong> {{ $servingName }}</li>@endif
                                                @if($displayCustomMenus->isNotEmpty())<li><strong>Menu:</strong> {{ $displayCustomMenus->map(fn($cm) => $cm->formatted_menu_name . ' (' . $cm->jumlah . ')')->join(', ') }}</li>@endif
                                                <li><strong>Pelengkap:</strong> {{ $displayCustomExtras->isNotEmpty() ? $displayCustomExtras->map(fn($e) => ($e->opsiKustom?->name ?? $e->formatted_menu_name) . ' (' . $e->jumlah . ')')->join(', ') : '-' }}</li>
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
                    <span>Rp {{ number_format($pesanan->subtotal,0,',','.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong class="fs-5">Total</strong>
                    <strong class="fs-5 text-success">Rp {{ number_format($pesanan->total,0,',','.') }}</strong>
                </div>
                <p class="small text-muted mb-1">Metode: Transfer (Midtrans)</p>
                <p class="small text-muted mb-1">Status: {{ $pesanan->status_pembayaran }}</p>
                
                @if($pesanan->refund_status && $pesanan->refund_status !== 'none')
                    <div class="alert alert-{{ $pesanan->refund_status === 'pending' ? 'warning' : 'success' }} py-2 mt-3 mb-0">
                        <i class="fa-solid fa-{{ $pesanan->refund_status === 'pending' ? 'clock' : 'check' }}"></i>
                        Refund: {{ $pesanan->refund_status === 'pending' ? 'Menunggu Refund' : 'Sudah Direfund' }}
                    </div>
                @endif
            </div>
        </div>

        @if(!in_array($pesanan->status, ['selesai','dibatalkan']))
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Aksi Pesanan</h3>
            </div>
            <div class="card-body">
                {{-- Form Update Status --}}
                <form id="statusForm" action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Update Status</label>
                        <select name="status" class="form-select">
                            @foreach(['diproses'=>'Diproses','dikirim'=>'Dikirim','selesai'=>'Selesai'] as $k=>$v)
                            <option value="{{ $k }}" {{ $pesanan->status==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3"><i class="fa-solid fa-sync"></i> Update Status</button>
                </form>

                {{-- Tombol Batalkan --}}
                <form id="cancelForm" action="{{ route('admin.pesanan.cancel', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="alasan_pembatalan" id="cancelReasonInput">
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
