@extends('layouts.app')
@section('title', 'Pilih Menu Acara')

@section('content')
<div class="container mx-auto px-4 py-8 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4 text-center">
                <!-- <h2 class="fs-3 fw-bold text-dark mt-2 mb-1">Pilih Menu untuk {{ $service->nama }}</h2> -->
                <h2 class="fs-3 fw-bold text-dark mt-2 mb-1">Pilih Menu untuk Acara Kantor</h2>
            </div>

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

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
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
                
                <!-- Card 1: Daftar Menu -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4 pt-2">
                        @forelse($menus as $menu)
                            @php
                                $detail = isset($pesanan) ? $pesanan->detailPesanans->firstWhere('menu_id', $menu->id) : null;
                                $porsiValue = $detail ? $detail->porsi : '';
                            @endphp
                            <div class="menu-section mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" data-menu-id="{{ $menu->id }}">
                                <div class="row align-items-start">
                                    <div class="col-12 col-md-3 mb-3 mb-md-0 text-center text-md-start">
                                        @if($menu->gambar)
                                            <img src="{{ asset('storage/menu/' . $menu->gambar) }}" class="img-fluid  shadow-sm" style="width: 100%; max-width: 200px; height: 200px; object-fit: cover;" alt="{{ $menu->nama_menu }}">
                                        @else
                                            <div class="bg-light border d-flex align-items-center justify-content-center  shadow-sm mx-auto mx-md-0" style="width: 100%; max-width: 200px; height: 200px;">
                                                <span class="text-muted small">Belum ada gambar</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <h5 class="fw-bold text-dark mb-2">{{ $menu->nama_menu }}</h5>
                                        <p class="text-dark small mb-3">
                                            {{ $menu->deskripsi }}
                                        </p>
                                        @if($menu->harga > 0)
                                            <div class="mb-3">
                                                <span class="fw-bold fs-6">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span> <span class="text-muted small">/ porsi</span>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-2">
                                            <label for="porsi_{{ $menu->id }}" class="form-label fw-semibold text-dark">Jumlah Porsi</label>
                                            <input type="number" class="form-control porsi-input @error('porsi_'.$menu->id) is-invalid @enderror" style="max-width: 200px;" name="porsi_{{ $menu->id }}" id="porsi_{{ $menu->id }}" value="{{ $porsiValue }}">
                                            <div class="invalid-feedback porsi-feedback">Minimal pemesanan 50 porsi.</div>
                                            @error('porsi_'.$menu->id)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text text-dark">Minimal pemesanan 50 porsi.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">Belum ada menu yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if(isset($minumans) && $minumans->count() > 0)
                <!-- Bagian Minuman -->
                <div class="card shadow-sm border-0 mb-4" id="minuman_section">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-1">Minuman</h5>
                        <p class="text-dark small mb-3">
                            Tambahan minuman untuk menyegarkan acara Anda.
                        </p>
                        
                        <div class="mb-3 checkbox-group">
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
                                <div class="form-check mb-2 d-flex justify-content-between align-items-center" style="max-width: 500px;">
                                    <div>
                                        <input class="form-check-input me-2 minuman-checkbox" type="checkbox" name="minuman_ids[]" value="{{ $minuman->id }}" id="minuman_{{ $minuman->id }}" {{ $isChecked ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" style="cursor: pointer;" for="minuman_{{ $minuman->id }}">
                                            {{ $minuman->nama_minuman }}
                                        </label>
                                    </div>
                                    <span class="text-dark small">
                                        Rp {{ number_format($minuman->harga, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                            <div class="invalid-feedback minuman-feedback d-none">Pilih minimal satu minuman.</div>
                            @error('minuman_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="jumlah_cup_minuman" class="form-label fw-semibold text-dark">Jumlah Cup</label>
                            <input type="number" class="form-control @error('jumlah_cup_minuman') is-invalid @enderror" 
                                   style="max-width: 300px;" 
                                   name="jumlah_cup_minuman" 
                                   id="jumlah_cup_minuman" 
                                   value="{{ old('jumlah_cup_minuman', $minumanValue ?: '') }}" 
                                   min="1">
                            <div class="invalid-feedback jumlah-cup-feedback d-none">Jumlah cup wajib diisi.</div>
                            @error('jumlah_cup_minuman')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-dark">Minimal 1 cup (berlaku untuk semua minuman yang dipilih).</div>
                        </div>
                    </div>
                </div>
                @endif
            
                <div class="d-flex justify-content-start">
                    <a href="{{ route('pelanggan.acara.lokasi') }}" class="btn btn-secondary px-4 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="d-flex justify-content-end  gap-2 mb-5">
                    @auth
                        <button type="submit" class="btn btn-warning text-white px-5 py-2 fw-bold shadow-sm">
                            Buat Pesanan
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning px-5 py-2 fw-bold shadow-sm">
                            Login untuk Memesan
                        </a>
                    @endauth
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formPilihMenu');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let isAnyMenuSelected = false;

                // Reset validation state
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.items-feedback, .minuman-feedback, .jumlah-cup-feedback').forEach(el => {
                    el.classList.remove('d-block');
                    el.classList.add('d-none');
                });

                document.querySelectorAll('.menu-section').forEach(section => {
                    const porsiInput = section.querySelector('.porsi-input');
                    
                    let hasPorsi = porsiInput.value && parseInt(porsiInput.value) > 0;

                    if (hasPorsi) {
                        isAnyMenuSelected = true;
                        
                        // Check minimum portion
                        if (parseInt(porsiInput.value) < 50) {
                            porsiInput.classList.add('is-invalid');
                            const porsiFeedback = section.querySelector('.porsi-feedback');
                            if (porsiFeedback) {
                                porsiFeedback.innerHTML = "Minimal pemesanan 50 porsi.";
                            }
                            isValid = false;
                        }
                    }
                });

                // Validasi Minuman
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
                        text: 'Terdapat kesalahan pada isian form. Silakan lengkapi data yang wajib.'
                    });
                    return false;
                }

                if (!isAnyMenuSelected) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan isi jumlah porsi minimal pada satu menu untuk memesan.'
                    });
                    return false;
                }
            });
        }
    });
</script>
@endsection
