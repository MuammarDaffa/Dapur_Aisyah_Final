@extends('layouts.app')
@section('title', 'Pilih Menu Harian')

@section('content')
<style>
    .page-header {
        padding: 60px 0 30px;
        background-color: #fff;
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
        border-bottom: 1px solid #dcdcdc;
    }
    
    .menu-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .date-badge-top {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: #fff;
        color: #333;
        padding: 4px 10px;
        border-radius: 4px;
        border: 1px solid #dcdcdc;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: capitalize;
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
                                
                                <div class="menu-img-container">
                                    @if($jadwal->menu->gambar)
                                        <img src="{{ asset('storage/menu/' . $jadwal->menu->gambar) }}" class="menu-img" alt="{{ $jadwal->menu->nama_menu }}">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center menu-img">
                                            <i class="fa-solid fa-image text-secondary opacity-25" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="date-badge-top shadow-sm">
                                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l') }}
                                    </div>
                                </div>
                                
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <h4 class="fw-bold text-dark mb-1">{{ $jadwal->menu->nama_menu }}</h4>
                                    <p class="fw-bold text-dark mb-3" style="font-size: 1.1rem;">{{ number_format($jadwal->menu->harga, 0, ',', '.') }}</p>
                                    
                                    <div class="mb-3">
                                        <span class="d-block text-secondary mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">KOMPOSISI</span>
                                        <p class="text-dark small mb-0" style="line-height: 1.4;">{{ $jadwal->menu->deskripsi }}</p>
                                    </div>
                                    
                                    <div class="mb-auto">
                                        @if($jadwal->menu->tambahanLaukPauk->count() > 0)
                                        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1" data-bs-toggle="modal" data-bs-target="#modalJadwal{{ $jadwal->id }}" style="font-size: 0.85rem;">
                                            + Tambahan Lauk
                                        </button>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-4 pt-2 d-flex justify-content-between align-items-end">
                                        <div class="pb-1">
                                            <span class="d-block fw-bold text-dark" style="font-size: 1.1rem;">Porsi</span>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <div class="mb-2 border border-secondary rounded px-2 py-1 text-dark" style="font-size: 0.75rem;">
                                                Sisa : {{ $jadwal->stok_tersisa }}
                                            </div>
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
                        // For the new layout, the date is in date-badge-top
                        const dateEl = section.querySelector('.date-badge-top');
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
