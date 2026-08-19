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
        <!-- Detail Pesanan Layout -->
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">Detail Pesanan: {{ $pesanan->nomor_pesanan }}</h3>
                <div class="ms-auto">
                    <span class="badge text-bg-{{ $pesanan->status_pembayaran_color }} mb-1">
                        {{ $pesanan->status_pembayaran_label }}
                    </span>
                    <span class="badge text-bg-{{ $pesanan->status_pesanan_color }}">
                        {{ $pesanan->status_pesanan_label }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-5">
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Pelanggan</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->user->name }}</div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Telepon</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->user->phone }}</div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Email</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1"><a href="mailto:{{ $pesanan->user->email }}" class="text-primary text-decoration-none">{{ $pesanan->user->email }}</a></div>
                    </div>
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Tipe Katering</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">Katering {{ ucfirst($pesanan->tipe_layanan) }}</div>
                    </div>
                    @if($pesanan->tanggal_pesanan)
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Tgl transaksi</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d M Y') }}</div>
                    </div>
                    @endif
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Metode</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ ucwords(str_replace('_', ' ', $pesanan->metode_pengambilan)) }}</div>
                    </div>
                    @if($pesanan->metode_pengambilan === 'diantar_ke_tempat')
                    <div class="d-flex mb-2">
                        <strong style="width: 150px; flex-shrink: 0;">Lokasi</strong>
                        <div style="width: 15px; flex-shrink: 0;">:</div>
                        <div class="flex-grow-1">{{ $pesanan->alamat_lengkap ?? '-' }}</div>
                    </div>
                    @endif
                </div>

                @if($pesanan->catatan)
                <div class="callout callout-info mb-4">
                    <h5><i class="fa-solid fa-note-sticky text-info"></i> Catatan Pesanan:</h5>
                    <p>{{ $pesanan->catatan }}</p>
                </div>
                @endif



                <h4 class="mb-3">Detail Pesanan</h4>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Tanggal</th>
                                <th>Menu</th>
                                <th>Porsi</th>
                                <th>Tambahan</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Filter out minuman from the main loop so they don't appear as empty menus
                                $menuDetails = $pesanan->detailPesanans->filter(function ($item) {
                                    return is_null($item->minuman_id);
                                });
                            @endphp
                            
                            @forelse($menuDetails as $detail)
                                @php
                                    $groupedItems = null;
                                    $rowspan = 1;
                                    if ($detail->tambahanLaukPauk && $detail->tambahanLaukPauk->count() > 0) {
                                        $groupedItems = $detail->tambahanLaukPauk->groupBy('id')->map(function ($items) {
                                            return (object) [
                                                'nama' => $items->first()->nama,
                                                'jumlah' => $items->count()
                                            ];
                                        })->values();
                                        $rowspan = $groupedItems->count();
                                    }
                                    
                                    $menuName = $detail->menu ? $detail->menu->nama_menu : ($detail->is_rescheduled ? '-' : '<span class="text-warning"><i class="fa-solid fa-clock"></i> Menunggu Jadwal Admin</span>');
                                    

                                    $formattedDate = $detail->tanggal_pengiriman ? \Carbon\Carbon::parse($detail->tanggal_pengiriman)->translatedFormat('d F Y') : '-';
                                @endphp

                                <tr>
                                    <td class="text-center" rowspan="{{ $rowspan }}">{{ $formattedDate }}</td>
                                    
                                    <td rowspan="{{ $rowspan }}">{!! $menuName !!}</td>
                                    <td class="text-center" rowspan="{{ $rowspan }}">{{ $detail->porsi }}</td>
                                    
                                    @if($groupedItems)
                                        <td>{{ $groupedItems[0]->nama }}</td>
                                        <td class="text-center">{{ $groupedItems[0]->jumlah }}</td>
                                    @else
                                        <td class="text-center text-muted">-</td>
                                        <td class="text-center text-muted">-</td>
                                    @endif
                                    
                                    <td class="text-end fw-bold" rowspan="{{ $rowspan }}">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>

                                @if($groupedItems && $groupedItems->count() > 1)
                                    @for($i = 1; $i < $rowspan; $i++)
                                        <tr>
                                            <td>{{ $groupedItems[$i]->nama }}</td>
                                            <td class="text-center">{{ $groupedItems[$i]->jumlah }}</td>
                                        </tr>
                                    @endfor
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Data menu tidak ditemukan.</td>
                                </tr>
                            @endforelse


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- <div class="card card-outline card-success mb-4">
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
                
                @if($pesanan->refund_status && $pesanan->refund_status !== 'none')
                    <div class="alert alert-{{ $pesanan->refund_status === 'pending' ? 'warning' : 'success' }} py-2 mt-3 mb-0">
                        <i class="fa-solid fa-{{ $pesanan->refund_status === 'pending' ? 'clock' : 'check' }}"></i>
                        Refund: {{ $pesanan->refund_status === 'pending' ? 'Menunggu Refund' : 'Sudah Direfund' }}
                    </div>
                @endif
            </div>
        </div> -->

        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Aksi Pesanan</h3>
            </div>
            <div class="card-body">
                {{-- Form Update Status --}}
                <form id="statusForm" action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Status Pesanan</label>
                        <select name="status_pesanan" class="form-select">
                            @foreach(['diproses'=>'Diproses','dibatalkan'=>'Dibatalkan','selesai'=>'Selesai'] as $k=>$v)
                            <option value="{{ $k }}" {{ $pesanan->status_pesanan==$k?'selected':'' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3"> Update Status</button>
                </form>


            </div>
        </div>
    </div>
</div>

@endsection
