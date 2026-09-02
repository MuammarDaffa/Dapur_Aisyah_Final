@extends('layouts.app')
@section('title', 'Pilih Menu Harian')

@section('content')
<style>
    .page-header {
        position: relative;
        padding: 80px 0 40px;
        background-color: var(--bg-cream);
        overflow: hidden;
    }
    
    .blob-header {
        position: absolute;
        top: -50px; left: -10%;
        width: 400px; height: 400px;
        background: var(--primary-terracotta);
        opacity: 0.05;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        animation: morph 15s ease-in-out infinite alternate;
        z-index: 0;
    }

    .menu-bento-card {
        border-radius: var(--bento-radius);
        background: white;
        box-shadow: var(--soft-shadow);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 2px solid transparent;
        overflow: hidden;
        position: relative;
    }
    .menu-bento-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary-terracotta);
        box-shadow: 0 25px 50px rgba(224, 93, 54, 0.12);
    }
    
    .menu-bento-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
        transition: transform 0.6s ease;
    }
    .menu-bento-card:hover .menu-bento-img {
        transform: scale(1.05);
    }

    .date-pill {
        background-color: var(--bg-cream);
        color: var(--text-dark);
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    .qty-input-wrapper {
        background: var(--bg-cream);
        border-radius: 50px;
        padding: 4px;
        display: inline-flex;
        align-items: center;
    }
    .qty-input {
        background: transparent;
        border: none;
        text-align: center;
        font-weight: 800;
        font-size: 1.2rem;
        width: 60px;
        color: var(--forest-green);
    }
    .qty-input:focus {
        outline: none;
    }
    
    .addon-item {
        background: var(--bg-cream);
        border-radius: 16px;
        padding: 12px;
        transition: all 0.3s ease;
    }
    .addon-item:hover {
        background: white;
        box-shadow: var(--soft-shadow);
    }
    
    .btn-fixed-bottom {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        box-shadow: 0 15px 35px rgba(224, 93, 54, 0.4);
    }
</style>

<div class="page-header">
    <div class="blob-header"></div>
    <div class="container position-relative z-1">
        <div class="text-center mb-4">
            <span class="d-inline-flex align-items-center gap-2 bg-white rounded-pill px-4 py-2 shadow-sm mb-3 border border-light">
                <span class="bg-primary-mc rounded-circle" style="width: 8px; height: 8px;"></span>
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Katering Harian</span>
            </span>
            <h1 class="display-4 fw-bold text-dark mb-3">
                Menu <span style="color: var(--forest-green); font-style: italic;">Harian</span>
            </h1>
            <p class="text-secondary fs-5 max-w-2xl mx-auto">Pilih menu sehat dan bergizi untuk hari-hari produktif Anda.</p>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-4 pb-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            @if($errors->any())
                <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                    <ul class="mb-0 fw-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pelanggan.harian.simpan') }}" method="POST" id="formPilihMenu">
                @csrf
                <input type="hidden" name="pesanan_id" value="{{ isset($pesanan) ? $pesanan->id : '' }}">

                <div class="row g-5 mb-5">
                    @forelse($jadwals as $jadwal)
                        @php
                            $porsiValue = 0;
                            $detailLama = null;
                            if (isset($pesanan)) {
                                $tanggalJadwalStr = \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d');
                                $detailLama = $pesanan->detailPesanans->first(function ($detail) use ($tanggalJadwalStr) {
                                    return \Carbon\Carbon::parse($detail->tanggal_pengiriman)->format('Y-m-d') === $tanggalJadwalStr;
                                });
                                if ($detailLama) {
                                    $porsiValue = $detailLama->porsi;
                                }
                            }
                        @endphp
                        <div class="col-lg-6">
                            <div class="jadwal-section menu-bento-card h-100 d-flex flex-column" data-jadwal-id="{{ $jadwal->id }}">
                                <input type="hidden" name="jadwal_ids[]" value="{{ $jadwal->id }}">
                                
                                <div class="position-relative overflow-hidden" style="border-radius: var(--bento-radius) var(--bento-radius) 30px 30px; z-index: 1;">
                                    @if($jadwal->menu->gambar)
                                        <img src="{{ asset('storage/menu/' . $jadwal->menu->gambar) }}" class="menu-bento-img" alt="{{ $jadwal->menu->nama_menu }}">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center menu-bento-img">
                                            <i class="fa-solid fa-leaf text-secondary opacity-25" style="font-size: 5rem;"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="position-absolute top-0 start-0 w-100 p-4 d-flex justify-content-between align-items-start">
                                        <div class="date-pill shadow-sm">
                                            <i class="fa-regular fa-calendar-check text-primary-mc"></i>
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('D, d M') }}
                                        </div>
                                        <span class="badge-mc-success shadow-sm">Sisa: {{ $jadwal->stok_tersisa }}</span>
                                    </div>
                                </div>
                                
                                <div class="p-4 p-md-5 d-flex flex-column flex-grow-1" style="margin-top: -20px; background: white; z-index: 2; border-radius: 30px;">
                                    <h3 class="fw-bold text-dark mb-1 fs-3">{{ $jadwal->menu->nama_menu }}</h3>
                                    <h4 class="fw-bold text-primary-mc mb-4">Rp {{ number_format($jadwal->menu->harga, 0, ',', '.') }}</h4>
                                    
                                    <div class="mb-4 bg-cream p-4 rounded-4" style="background-color: var(--bg-cream); flex-grow: 1;">
                                        <span class="fw-bold d-block mb-2 text-secondary small text-uppercase tracking-wider">
                                            <i class="fa-solid fa-utensils me-2"></i>Komposisi Menu
                                        </span>
                                        <p class="text-dark mb-0 fw-medium" style="line-height: 1.7;">{{ $jadwal->menu->deskripsi }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <span class="fw-bold d-block mb-3 text-secondary small text-uppercase tracking-wider">
                                            <i class="fa-solid fa-plus me-2"></i>Tambahan Lauk
                                        </span>
                                        @if($jadwal->menu->tambahanLaukPauk->count() > 0)
                                            <div class="d-flex flex-column gap-2">
                                            @foreach($jadwal->menu->tambahanLaukPauk as $item)
                                                @php
                                                    $itemCount = 0;
                                                    if ($detailLama && $detailLama->tambahanLaukPauk) {
                                                        $itemCount = $detailLama->tambahanLaukPauk->where('id', $item->id)->count();
                                                    }
                                                @endphp
                                                <div class="addon-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="d-block fw-bold text-dark">{{ $item->nama }}</span>
                                                        <span class="text-primary-mc small fw-bold">+Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="qty-input-wrapper" style="background: white;">
                                                        <input type="number" class="qty-input" name="items_{{ $jadwal->id }}[{{ $item->id }}]" value="{{ $itemCount }}" min="0">
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                        @else
                                            <div class="addon-item text-center py-3">
                                                <p class="text-muted small mb-0 fw-medium">Tidak ada tambahan tersedia</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto pt-3 border-top border-2 border-light d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="d-block fw-bold text-dark fs-5">Porsi Utama</span>
                                            <span class="text-secondary small">Minimal 1 porsi</span>
                                        </div>
                                        <div class="qty-input-wrapper shadow-sm border border-light">
                                            <input type="number" class="qty-input" name="porsi_{{ $jadwal->id }}" value="{{ $porsiValue }}" min="0" style="font-size: 1.5rem; width: 70px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="bg-white p-5 rounded-4 border-0 shadow-sm mx-auto" style="max-width: 500px; border-radius: var(--bento-radius) !important;">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-leaf text-secondary fs-1"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-2">Belum Ada Jadwal</h3>
                                <p class="text-secondary mb-0">Jadwal menu harian sedang disiapkan. Silakan cek kembali nanti.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if(count($jadwals) > 0)
                    @auth
                        <button type="submit" class="btn btn-primary-mc btn-lg rounded-pill px-5 py-4 btn-fixed-bottom d-flex align-items-center gap-3" id="btnSimpan">
                            <span class="fs-5 fw-bold">Lanjutkan Pemesanan</span>
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fa-solid fa-arrow-right text-primary-mc"></i>
                            </div>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary-mc btn-lg rounded-pill px-5 py-4 btn-fixed-bottom text-decoration-none d-flex align-items-center gap-3">
                            <i class="fa-solid fa-lock fs-5"></i>
                            <span class="fs-5 fw-bold">Login untuk Memesan</span>
                        </a>
                    @endauth
                @endif
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formPilihMenu = document.getElementById('formPilihMenu');
        if(formPilihMenu) {
            formPilihMenu.addEventListener('submit', function(e) {
                let totalPorsi = 0;
                let hasTambahanWithoutPorsi = false;
                let firstInvalidDate = "";

                const jadwalSections = document.querySelectorAll('.jadwal-section');
                jadwalSections.forEach(section => {
                    const jadwalId = section.getAttribute('data-jadwal-id');
                    const porsiInput = section.querySelector(`input[name="porsi_${jadwalId}"]`);
                    const porsi = parseInt(porsiInput.value) || 0;
                    
                    totalPorsi += porsi;
                    
                    const itemsInputs = section.querySelectorAll(`input[name^="items_${jadwalId}"]`);
                    let totalItems = 0;
                    itemsInputs.forEach(input => {
                        totalItems += parseInt(input.value) || 0;
                    });
                    
                    if (totalItems > 0 && porsi === 0) {
                        hasTambahanWithoutPorsi = true;
                        const dateEl = section.querySelector('.date-pill');
                        if (dateEl && !firstInvalidDate) {
                            firstInvalidDate = dateEl.innerText.trim();
                        }
                    }
                });

                if (hasTambahanWithoutPorsi) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops!',
                        text: `Anda memesan menu tambahan pada jadwal ${firstInvalidDate || 'tertentu'}, namun belum mengisi porsi utamanya. Silakan isi porsi utama minimal 1.`,
                        confirmButtonColor: '#f97316'
                    });
                    return;
                }
                
                if (totalPorsi === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Ada Pesanan',
                        text: 'Silakan masukkan jumlah porsi utama (minimal 1) pada jadwal katering yang Anda inginkan.',
                        confirmButtonColor: '#f97316'
                    });
                    return;
                }
            });
        }
    });
</script>
@endsection
