@extends('layouts.app')
@section('title', 'Pilih Menu Harian')

@section('content')
<style>
    .page-header {
        padding: 60px 0 30px;
        background-color: #fff;
    }
    
    .menu-card {
        border-radius: 12px;
        background: #fff;
        border: 1px solid #eaeaea;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    
    .menu-card:hover {
        border-color: #dcdcdc;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .menu-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .date-badge {
        background-color: #f8f9fa;
        color: #333;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 1px solid #eaeaea;
    }

    .qty-stepper {
        display: flex;
        align-items: center;
        border: 1px solid #eaeaea;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .qty-stepper .btn-step {
        background: #f8f9fa;
        border: none;
        color: #333;
        width: 30px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .qty-stepper .btn-step:hover {
        background: #e9ecef;
    }

    .qty-stepper .qty-input {
        border: none;
        border-left: 1px solid #eaeaea;
        border-right: 1px solid #eaeaea;
        border-radius: 0;
        width: 40px;
        height: 32px;
        text-align: center;
        font-weight: 600;
        padding: 0;
    }
    .qty-stepper .qty-input:focus {
        outline: none;
    }
    
    /* Remove arrows from number input */
    .qty-stepper .qty-input::-webkit-outer-spin-button,
    .qty-stepper .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .qty-stepper .qty-input[type=number] {
        -moz-appearance: textfield;
    }
    
    .addon-item {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .addon-item:last-child {
        border-bottom: none;
    }
    
    .btn-fixed-bottom {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 8px !important;
    }
</style>

<div class="page-header">
    <div class="container">
        <div class="mb-4">
            <h1 class="fs-2 fw-bold text-dark mb-2">Menu Katering Harian</h1>
            <p class="text-secondary mb-0">Pilih menu sehat untuk jadwal Anda.</p>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-12">
            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pelanggan.harian.simpan') }}" method="POST" id="formPilihMenu">
                @csrf
                <input type="hidden" name="pesanan_id" value="{{ isset($pesanan) ? $pesanan->id : '' }}">

                <div class="row g-4 mb-5">
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
                        <div class="col-md-6 col-lg-4">
                            <div class="jadwal-section menu-card h-100 d-flex flex-column" data-jadwal-id="{{ $jadwal->id }}">
                                <input type="hidden" name="jadwal_ids[]" value="{{ $jadwal->id }}">
                                
                                <div class="position-relative">
                                    @if($jadwal->menu->gambar)
                                        <img src="{{ asset('storage/menu/' . $jadwal->menu->gambar) }}" class="menu-img" alt="{{ $jadwal->menu->nama_menu }}">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center menu-img">
                                            <i class="fa-solid fa-image text-secondary opacity-25" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="position-absolute top-0 start-0 w-100 p-3 d-flex justify-content-between align-items-center">
                                        <div class="date-badge">
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l') }}
                                        </div>
                                        <span class="badge bg-dark text-white rounded-2 px-2 py-1" style="font-weight: 500;">Sisa: {{ $jadwal->stok_tersisa }}</span>
                                    </div>
                                </div>
                                
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h3 class="fw-bold text-dark fs-5 mb-0">{{ $jadwal->menu->nama_menu }}</h3>
                                    </div>
                                    <p class="fw-semibold text-danger mb-3">Rp {{ number_format($jadwal->menu->harga, 0, ',', '.') }}</p>
                                    
                                    <div class="mb-4">
                                        <span class="d-block fw-semibold text-secondary small text-uppercase mb-1">Komposisi</span>
                                        <p class="text-secondary small mb-0">{{ $jadwal->menu->deskripsi }}</p>
                                    </div>
                                    
                                    <div class="mb-4 flex-grow-1">
                                        <span class="d-block fw-semibold text-secondary small text-uppercase mb-2">Tambahan</span>
                                        @if($jadwal->menu->tambahanLaukPauk->count() > 0)
                                            <div class="d-flex flex-column">
                                            @foreach($jadwal->menu->tambahanLaukPauk as $item)
                                                @php
                                                    $itemCount = 0;
                                                    if ($detailLama && $detailLama->tambahanLaukPauk) {
                                                        $itemCount = $detailLama->tambahanLaukPauk->where('id', $item->id)->count();
                                                    }
                                                @endphp
                                                <div class="addon-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="d-block text-dark small">{{ $item->nama }}</span>
                                                        <span class="text-danger small">+Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="qty-stepper">
                                                        <button type="button" class="btn-step btn-minus">-</button>
                                                        <input type="number" class="qty-input" name="items_{{ $jadwal->id }}[{{ $item->id }}]" value="{{ $itemCount }}" min="0">
                                                        <button type="button" class="btn-step btn-plus">+</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted small mb-0">-</p>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="d-block fw-semibold text-dark">Porsi</span>
                                        </div>
                                        <div class="qty-stepper" style="border-color: #ccc;">
                                            <button type="button" class="btn-step btn-minus" style="width: 36px; height: 38px; background: #eee;">-</button>
                                            <input type="number" class="qty-input" name="porsi_{{ $jadwal->id }}" value="{{ $porsiValue }}" min="0" style="width: 50px; height: 38px; font-size: 1.1rem; border-color: #ccc;">
                                            <button type="button" class="btn-step btn-plus" style="width: 36px; height: 38px; background: #eee;">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="p-5 mx-auto" style="max-width: 500px;">
                                <i class="fa-regular fa-calendar-xmark text-muted mb-3" style="font-size: 3rem;"></i>
                                <h4 class="fw-semibold text-dark">Belum Ada Jadwal</h4>
                                <p class="text-secondary">Jadwal menu harian belum tersedia untuk saat ini.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if(count($jadwals) > 0)
                    @auth
                        <button type="submit" class="btn btn-dark px-4 py-2 btn-fixed-bottom d-flex align-items-center gap-2" id="btnSimpan">
                            <span>Lanjutkan Pemesanan</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-dark px-4 py-2 btn-fixed-bottom text-decoration-none d-flex align-items-center gap-2">
                            <i class="fa-solid fa-lock"></i>
                            <span>Login untuk Memesan</span>
                        </a>
                    @endauth
                @endif
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Stepper Logic
        document.querySelectorAll('.qty-stepper').forEach(stepper => {
            const btnMinus = stepper.querySelector('.btn-minus');
            const btnPlus = stepper.querySelector('.btn-plus');
            const input = stepper.querySelector('.qty-input');

            btnMinus.addEventListener('click', () => {
                let val = parseInt(input.value) || 0;
                if (val > parseInt(input.min || 0)) {
                    input.value = val - 1;
                }
            });

            btnPlus.addEventListener('click', () => {
                let val = parseInt(input.value) || 0;
                input.value = val + 1;
            });
        });

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
                        const dateEl = section.querySelector('.date-badge');
                        if (dateEl && !firstInvalidDate) {
                            firstInvalidDate = dateEl.innerText.trim();
                        }
                    }
                });

                if (hasTambahanWithoutPorsi) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: `Anda memesan menu tambahan pada jadwal ${firstInvalidDate || 'tertentu'}, namun belum mengisi porsi utamanya. Silakan isi porsi utama minimal 1.`,
                        confirmButtonColor: '#212529'
                    });
                    return;
                }
                
                if (totalPorsi === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Ada Pesanan',
                        text: 'Silakan masukkan jumlah porsi utama (minimal 1) pada jadwal katering yang Anda inginkan.',
                        confirmButtonColor: '#212529'
                    });
                    return;
                }
            });
        }
    });
</script>
@endsection
