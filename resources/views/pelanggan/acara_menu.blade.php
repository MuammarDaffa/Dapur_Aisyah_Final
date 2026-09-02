@extends('layouts.app')
@section('title', 'Pilih Menu Acara')

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
        background: var(--forest-green);
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
        width: 70px;
        color: var(--primary-terracotta);
    }
    .qty-input:focus {
        outline: none;
    }
    
    .bento-addon-box {
        border-radius: var(--bento-radius);
        background: white;
        box-shadow: var(--soft-shadow);
        padding: 40px;
        margin-bottom: 24px;
    }

    .drink-check-card {
        background: var(--bg-cream);
        border: 2px solid transparent;
        border-radius: 20px;
        padding: 16px 20px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .drink-check-card:hover {
        background: white;
        border-color: #E2E8F0;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .drink-check-card.checked {
        background: white;
        border-color: var(--primary-terracotta);
        box-shadow: 0 10px 25px rgba(224, 93, 54, 0.1);
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
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Katering Acara Kantoran</span>
            </span>
            <h1 class="display-4 fw-bold text-dark mb-3">
                Pilih Menu <span style="color: var(--primary-terracotta); font-style: italic;">Spesial</span>
            </h1>
            <p class="text-secondary fs-5 max-w-2xl mx-auto">Sajikan pengalaman kuliner premium untuk acara kantor atau momen penting Anda.</p>
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

            <form action="{{ route('pelanggan.acara.simpan') }}" method="POST" id="formPilihMenu">
                @csrf
                @if(isset($pesanan))
                    <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">
                @endif
                
                <div class="row g-5 mb-5">
                    @forelse($menus as $menu)
                        @php
                            $detail = isset($pesanan) ? $pesanan->detailPesanans->firstWhere('menu_id', $menu->id) : null;
                            $porsiValue = $detail ? $detail->porsi : '';
                        @endphp
                        <div class="col-lg-6">
                            <div class="menu-section menu-bento-card h-100 d-flex flex-column" data-menu-id="{{ $menu->id }}">
                                
                                <div class="position-relative overflow-hidden" style="border-radius: var(--bento-radius) var(--bento-radius) 30px 30px; z-index: 1;">
                                    @if($menu->gambar)
                                        <img src="{{ asset('storage/menu/' . $menu->gambar) }}" class="menu-bento-img" alt="{{ $menu->nama_menu }}">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center menu-bento-img">
                                            <i class="fa-solid fa-star text-secondary opacity-25" style="font-size: 5rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-4 p-md-5 d-flex flex-column flex-grow-1" style="margin-top: -20px; background: white; z-index: 2; border-radius: 30px;">
                                    <h3 class="fw-bold text-dark mb-1 fs-3">{{ $menu->nama_menu }}</h3>
                                    @if($menu->harga > 0)
                                        <h4 class="fw-bold text-primary-mc mb-4">Rp {{ number_format($menu->harga, 0, ',', '.') }} <span class="fs-6 text-secondary fw-medium">/ porsi</span></h4>
                                    @endif
                                    
                                    <div class="mb-4 bg-cream p-4 rounded-4" style="background-color: var(--bg-cream); flex-grow: 1;">
                                        <span class="fw-bold d-block mb-2 text-secondary small text-uppercase tracking-wider">
                                            <i class="fa-solid fa-utensils me-2"></i>Komposisi Menu
                                        </span>
                                        <p class="text-dark mb-0 fw-medium" style="line-height: 1.7;">{{ $menu->deskripsi }}</p>
                                    </div>
                                    
                                    <div class="mt-auto pt-3 border-top border-2 border-light d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="d-block fw-bold text-dark fs-5">Porsi Pesanan</span>
                                                <span class="text-secondary small">Minimal 50 porsi</span>
                                            </div>
                                            <div class="qty-input-wrapper shadow-sm border border-light">
                                                <input type="number" class="qty-input porsi-input @error('porsi_'.$menu->id) is-invalid @enderror" name="porsi_{{ $menu->id }}" id="porsi_{{ $menu->id }}" value="{{ $porsiValue }}" min="0">
                                            </div>
                                        </div>
                                        <div class="invalid-feedback porsi-feedback fw-bold text-end w-100">Minimal pemesanan 50 porsi.</div>
                                        @error('porsi_'.$menu->id)
                                            <div class="invalid-feedback d-block fw-bold text-end w-100">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="bg-white p-5 rounded-4 border-0 shadow-sm mx-auto" style="max-width: 500px; border-radius: var(--bento-radius) !important;">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-calendar-star text-secondary fs-1"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-2">Belum Ada Menu</h3>
                                <p class="text-secondary mb-0">Menu untuk acara sedang disiapkan. Silakan cek kembali nanti.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                @if(isset($minumans) && $minumans->count() > 0)
                <div class="bento-addon-box" id="minuman_section">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-glass-water text-primary-mc fs-4"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-0">Tambahan Minuman</h3>
                            <p class="text-secondary mb-0">Sempurnakan hidangan acara Anda dengan minuman segar pilihan.</p>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4 checkbox-group">
                        @foreach($minumans as $minuman)
                            @php
                                $isChecked = false;
                                $minumanValue = '';
                                if (old('minuman_ids')) {
                                    if (in_array($minuman->id, old('minuman_ids'))) {
                                        $isChecked = true;
                                    }
                                } elseif (isset($pesanan) && $pesanan->detailPesanans) {
                                    $detailMinuman = $pesanan->detailPesanans->where('minuman_id', $minuman->id)->first();
                                    if($detailMinuman) {
                                        $isChecked = true;
                                        $minumanValue = $detailMinuman->porsi;
                                    }
                                }
                            @endphp
                            <div class="col-md-6">
                                <label class="drink-check-card w-100 d-flex justify-content-between align-items-center {{ $isChecked ? 'checked' : '' }}" for="minuman_{{ $minuman->id }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <input class="form-check-input minuman-checkbox m-0 border-secondary shadow-none" type="checkbox" name="minuman_ids[]" value="{{ $minuman->id }}" id="minuman_{{ $minuman->id }}" {{ $isChecked ? 'checked' : '' }} style="width: 1.5rem; height: 1.5rem; border-radius: 6px;">
                                        <span class="fw-bold text-dark fs-5">{{ $minuman->nama_minuman }}</span>
                                    </div>
                                    <span class="text-primary-mc fw-bold bg-white px-3 py-1 rounded-pill shadow-sm">+Rp {{ number_format($minuman->harga, 0, ',', '.') }}</span>
                                </label>
                            </div>
                        @endforeach
                        <div class="col-12"><div class="invalid-feedback minuman-feedback d-none fw-bold">Pilih minimal satu minuman.</div></div>
                        @error('minuman_ids')
                            <div class="col-12"><div class="invalid-feedback d-block fw-bold">{{ $message }}</div></div>
                        @enderror
                    </div>

                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between bg-cream p-4 rounded-4 border border-light" style="background-color: var(--bg-cream);">
                        <div class="mb-3 mb-sm-0">
                            <span class="d-block fw-bold text-dark fs-5">Jumlah Cup</span>
                            <span class="text-secondary small">Berlaku untuk semua jenis minuman terpilih.</span>
                        </div>
                        <div class="qty-input-wrapper shadow-sm bg-white" style="border: 2px solid #E2E8F0;">
                            <input type="number" class="qty-input @error('jumlah_cup_minuman') is-invalid @enderror" name="jumlah_cup_minuman" id="jumlah_cup_minuman" value="{{ old('jumlah_cup_minuman', $minumanValue ?: '') }}" min="1" placeholder="0">
                        </div>
                    </div>
                    <div class="invalid-feedback jumlah-cup-feedback d-none fw-bold text-end mt-2">Jumlah cup wajib diisi minimal 1.</div>
                    @error('jumlah_cup_minuman')
                        <div class="invalid-feedback d-block fw-bold text-end mt-2">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="bento-addon-box">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-pen-to-square text-primary-mc fs-4"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">Catatan Khusus</h3>
                    </div>
                    <textarea class="form-control form-control-mc @error('catatan') is-invalid @enderror" name="catatan" id="catatan" rows="4" placeholder="Contoh: Tolong disajikan prasmanan dan bumbu pisah.">{{ old('catatan', isset($pesanan) ? $pesanan->catatan : '') }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback d-block fw-bold mt-2">{{ $message }}</div>
                    @enderror
                </div>
            
                <div class="d-flex justify-content-between align-items-center mb-5 pb-5 mt-4">
                    <a href="{{ route('pelanggan.acara.lokasi') }}" class="btn btn-outline-dark px-4 py-3 fw-bold rounded-pill border-2 hover-bg-dark">
                        <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                    </a>

                    @auth
                        <button type="submit" class="btn btn-primary-mc btn-lg rounded-pill px-5 py-4 btn-fixed-bottom d-flex align-items-center gap-3 shadow-lg" id="btnSimpan">
                            <span class="fs-5 fw-bold">Pesan Sekarang</span>
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fa-solid fa-arrow-right text-primary-mc"></i>
                            </div>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary-mc btn-lg rounded-pill px-5 py-4 btn-fixed-bottom text-decoration-none d-flex align-items-center gap-3 shadow-lg">
                            <i class="fa-solid fa-lock fs-5"></i>
                            <span class="fs-5 fw-bold">Login untuk Memesan</span>
                        </a>
                    @endauth
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Check Card style
        const drinkCards = document.querySelectorAll('.drink-check-card');
        drinkCards.forEach(card => {
            const checkbox = card.querySelector('.minuman-checkbox');
            checkbox.addEventListener('change', function() {
                if(this.checked) {
                    card.classList.add('checked');
                } else {
                    card.classList.remove('checked');
                }
            });
        });

        const form = document.getElementById('formPilihMenu');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let isAnyMenuSelected = false;

                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.minuman-feedback, .jumlah-cup-feedback, .porsi-feedback').forEach(el => {
                    el.classList.remove('d-block');
                    el.classList.add('d-none');
                });

                document.querySelectorAll('.menu-section').forEach(section => {
                    const porsiInput = section.querySelector('.porsi-input');
                    let hasPorsi = porsiInput.value && parseInt(porsiInput.value) > 0;

                    if (hasPorsi) {
                        isAnyMenuSelected = true;
                        if (parseInt(porsiInput.value) < 50) {
                            porsiInput.classList.add('is-invalid');
                            const porsiFeedback = section.querySelector('.porsi-feedback');
                            if (porsiFeedback) {
                                porsiFeedback.classList.remove('d-none');
                                porsiFeedback.classList.add('d-block');
                            }
                            isValid = false;
                        }
                    }
                });

                const minumanSection = document.getElementById('minuman_section');
                if (minumanSection) {
                    const minumanCheckboxes = minumanSection.querySelectorAll('.minuman-checkbox');
                    const jumlahCupInput = document.getElementById('jumlah_cup_minuman');
                    
                    let isMinumanChecked = false;
                    minumanCheckboxes.forEach(cb => {
                        if (cb.checked) isMinumanChecked = true;
                    });
                    
                    let hasJumlahCup = jumlahCupInput.value && parseInt(jumlahCupInput.value) > 0;

                    if (isMinumanChecked && !hasJumlahCup) {
                        jumlahCupInput.classList.add('is-invalid');
                        const cupFeedback = minumanSection.querySelector('.jumlah-cup-feedback');
                        if (cupFeedback) {
                            cupFeedback.classList.remove('d-none');
                            cupFeedback.classList.add('d-block');
                        }
                        isValid = false;
                    } else if (hasJumlahCup && !isMinumanChecked) {
                        const minumanFeedback = minumanSection.querySelector('.minuman-feedback');
                        if (minumanFeedback) {
                            minumanFeedback.classList.remove('d-none');
                            minumanFeedback.classList.add('d-block');
                        }
                        isValid = false;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Terdapat kesalahan pada isian form. Silakan lengkapi data yang wajib.',
                        confirmButtonColor: '#f97316'
                    });
                    return false;
                }

                if (!isAnyMenuSelected) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Ada Pesanan',
                        text: 'Silakan isi jumlah porsi minimal pada satu menu untuk memesan.',
                        confirmButtonColor: '#f97316'
                    });
                    return false;
                }
            });
        }
    });
</script>
@endsection
