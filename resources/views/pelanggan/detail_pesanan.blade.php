@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="fs-3 fw-bold text-dark mt-2 mb-4">Detail Pesanan Anda</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold fs-5">
                    Ringkasan Acara
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 200px;">Layanan Katering</td>
                            <td class="fw-semibold">{{ $layanan->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Acara</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($draft['tanggal_acara'])->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode Pengambilan</td>
                            <td class="fw-semibold">
                                @if($draft['metode_pengambilan'] == 'diantar_ke_tempat')
                                    Di Antar ke Lokasi
                                @else
                                    Ambil Sendiri
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe Penyajian</td>
                            <td class="fw-semibold">{{ $draft['tipe_penyajian'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold fs-5">
                    Detail Menu
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $menu->nama_menu }}</h6>
                    
                    @if(count($draft['item_menu']) > 0)
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($draft['item_menu'] as $item)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span>&bull; {{ $item['nama'] }}</span>
                                    @if($item['harga'] > 0)
                                        <span class="text-muted">+ Rp {{ number_format($item['harga'], 0, ',', '.') }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 200px;">Jumlah Porsi</td>
                            <td class="fw-semibold">{{ $draft['porsi'] }} porsi</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga per Porsi</td>
                            <td class="fw-semibold">Rp {{ number_format($draft['subtotal'] / $draft['porsi'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-5 bg-light">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-0">Total Pembayaran</p>
                        <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($draft['total'], 0, ',', '.') }}</h3>
                    </div>
                    <form action="{{ route('pelanggan.acara.bayar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-bold">Bayar</button>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-start mb-5">
                <a href="{{ route('pelanggan.acara.pilih_menu') }}" class="btn btn-secondary px-5 py-2 fw-bold shadow-sm">
                    Kembali
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
