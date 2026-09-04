@extends('layouts.app')
@section('title', 'Pilih Menu Harian')

@section('content')
<style>
    .page-header {
        padding: 60px 40px;
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    
    .header-slider {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 0;
    }
    
    .slider-img {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 2s ease-in-out;
    }
    
    .slider-img.active {
        opacity: 1;
    }
    
    .slider-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 100%);
        z-index: 1;
    }

    .page-header h1, .page-header p {
        position: relative;
        z-index: 2;
        color: #fff !important;
    }
    
    .menu-card {
        border-radius: 28px;
        background: #fff;
        border: 1px solid #dcdcdc;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    
    .menu-card:hover {
        border-color: #b0b0b0;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    
    .menu-img-container {
        position: relative;
    }
    
    @media (max-width: 767px) {
        .menu-img-container {
            max-width: 100% !important;
        }
    }

    .qty-stepper {
        display: flex;
        align-items: center;
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        overflow: hidden;
        background: #f8f9fa;
    }
    
    .qty-stepper .btn-step {
        background: transparent;
        border: none;
        color: #333;
        width: 32px;
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
        border-left: 1px solid #dcdcdc;
        border-right: 1px solid #dcdcdc;
        background: #fff;
        border-radius: 0;
        width: 45px;
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

@php
    $bgImages = collect($jadwals)->map(function($j) { 
        return $j->menu->gambar ? asset('storage/menu/' . $j->menu->gambar) : null; 
    })->filter()->values();
@endphp
<div class="container pt-4 mt-2">
    <div class="page-header">
        @if($bgImages->count() > 0)
        <div class="header-slider">
            @foreach($bgImages as $index => $img)
                <div class="slider-img {{ $index == 0 ? 'active' : '' }}" style="background-image: url('{{ $img }}')"></div>
            @endforeach
            <div class="slider-overlay"></div>
        </div>
        @else
        <div class="header-slider" style="background-color: #212529;"></div>
        @endif

        <div class="position-relative z-2 px-2">
            <h1 class="fs-2 fw-bold mb-2">Menu Katering Harian</h1>
            <!-- <p class="mb-0" style="color: rgba(255,255,255,0.85) !important;">Pilih menu sehat untuk jadwal Anda.</p> -->
        </div>
    </div>
</div>

<div class="container py-5 mt-3">
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

                <div class="row g-4 mb-5 justify-content-center">
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
                        <div class="col-12 col-md-10 col-lg-7">
                            <div class="jadwal-section menu-card" data-jadwal-id="{{ $jadwal->id }}">
                                <input type="hidden" name="jadwal_ids[]" value="{{ $jadwal->id }}">
                                
                                <div class="d-flex flex-column flex-md-row">
                                    <div class="menu-img-container flex-shrink-0 p-3 pb-md-3 pb-0" style="width: 100%; max-width: 220px;">
                                        @if($jadwal->menu->gambar)
                                            <img src="{{ asset('storage/menu/' . $jadwal->menu->gambar) }}" class="w-100 rounded-4" style="object-fit: cover; aspect-ratio: 1 / 1;" alt="{{ $jadwal->menu->nama_menu }}">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center w-100 rounded-4" style="aspect-ratio: 1 / 1;">
                                                <i class="fa-solid fa-image text-secondary opacity-25" style="font-size: 3rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="p-4 d-flex flex-column flex-grow-1">
                                        <div class="mb-3">
                                            <span class="text-uppercase fw-bold text-secondary small d-block mb-1" style="letter-spacing: 1px;">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l') }}</span>
                                            <h4 class="fw-bold text-dark mb-1">{{ $jadwal->menu->nama_menu }}</h4>
                                            <p class="fw-bold text-dark mb-2" style="font-size: 1.1rem;">Rp{{ number_format($jadwal->menu->harga, 0, ',', '.') }}</p>
                                            <p class="text-dark small mb-0" style="line-height: 1.5;">{{ $jadwal->menu->deskripsi }}</p>
                                        </div>
                                        
                                        <div class="mt-auto pt-3 d-flex justify-content-between align-items-end">
                                            <div class="mb-5 pb-1">
                                                @if($jadwal->menu->tambahanLaukPauk->count() > 0)
                                                <button type="button" class="btn btn-dark border-0 btn-sm rounded-pill px-3 py-1" data-bs-toggle="modal" data-bs-target="#modalJadwal{{ $jadwal->id }}" style="font-size: 0.85rem; background-color: #212529; color: #fff;">
                                                    + Tambahan Lauk
                                                </button>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="text-secondary small mb-2 fw-medium">Tersedia {{ $jadwal->stok_tersisa }} porsi</span>
                                                <div class="qty-stepper">
                                                    <button type="button" class="btn-step btn-minus">-</button>
                                                    <input type="number" class="qty-input" name="porsi_{{ $jadwal->id }}" value="{{ $porsiValue }}" min="0">
                                                    <button type="button" class="btn-step btn-plus">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($jadwal->menu->tambahanLaukPauk->count() > 0)
                        <!-- Modal for Jadwal {{ $jadwal->id }} -->
                        <div class="modal fade" id="modalJadwal{{ $jadwal->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">{{ $jadwal->menu->nama_menu }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-secondary small mb-3">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d M Y') }}</p>
                                        
                                        <h6 class="fw-bold mb-3 text-dark">Tambahan Lauk</h6>
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
                                                <div class="qty-stepper" style="border-color: #ccc;">
                                                    <button type="button" class="btn-step btn-minus">-</button>
                                                    <input type="number" class="qty-input" style="border-color: #ccc;" name="items_{{ $jadwal->id }}[{{ $item->id }}]" value="{{ $itemCount }}" min="0">
                                                    <button type="button" class="btn-step btn-plus">+</button>
                                                </div>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-dark w-100 rounded-pill py-2" data-bs-dismiss="modal">Simpan Tambahan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="p-5 mx-auto" style="max-width: 500px;">
                                <i class="fa-regular fa-calendar-xmark text-muted mb-3" style="font-size: 3rem;"></i>
                                <h4 class="fw-bold text-dark">Belum Ada Jadwal</h4>
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
        // Slider Logic
        const slides = document.querySelectorAll('.slider-img');
        if (slides.length > 1) {
            let currentSlide = 0;
            setInterval(() => {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }, 4000);
        }

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
                    
                    const modal = document.getElementById(`modalJadwal${jadwalId}`);
                    let totalItems = 0;
                    if(modal) {
                        const itemsInputs = modal.querySelectorAll(`input[name^="items_${jadwalId}"]`);
                        itemsInputs.forEach(input => {
                            totalItems += parseInt(input.value) || 0;
                        });
                    }
                    
                    if (totalItems > 0 && porsi === 0) {
                        hasTambahanWithoutPorsi = true;
                        const dateEl = section.querySelector('.text-uppercase');
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
