@extends('layouts.app')
@section('title', 'Pilih Menu Acara')

@section('content')
<div class="container mx-auto px-4 py-8 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <!-- <h2 class="fs-3 fw-bold text-dark mt-2 mb-1">Pilih Menu untuk {{ $service->nama }}</h2> -->
                <h2 class="fs-3 fw-bold text-dark mt-2 mb-1">Pilih Menu untuk Katering Acara Kantor</h2>
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
                
                @php
                    $menuPondokan = $menus->where('kategori_penyajian', 'prasmanan_saja');
                    $menuUtama = $menus->where('kategori_penyajian', 'bisa_pilih');
                @endphp

                <!-- Card 1: Menu Pondokan -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="fw-bold text-dark mb-0">MENU PONDOKAN / GUBUKAN</h4>
                        <p class="text-muted small">Semua menu di kategori ini otomatis disajikan secara prasmanan di lokasi.</p>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @forelse($menuPondokan as $menu)
                            @php
                                $detail = isset($pesanan) ? $pesanan->detailPesanans->firstWhere('menu_id', $menu->id) : null;
                                $porsiValue = $detail ? $detail->porsi : '';
                            @endphp
                            <div class="menu-section mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" data-menu-id="{{ $menu->id }}">
                                <h5 class="fw-bold text-dark mb-1">{{ $menu->nama_menu }}</h5>
                                <p class="text-dark small mb-3">
                                    {{ $menu->deskripsi }}
                                    @if($menu->harga > 0)
                                        <br><strong>Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong> / porsi
                                    @endif
                                </p>
                                
                                <div class="mb-2">
                                    <label for="porsi_{{ $menu->id }}" class="form-label fw-semibold text-dark">Jumlah Porsi</label>
                                    <input type="number" class="form-control porsi-input @error('porsi_'.$menu->id) is-invalid @enderror" style="max-width: 300px;" name="porsi_{{ $menu->id }}" id="porsi_{{ $menu->id }}" value="{{ $porsiValue }}">
                                    <div class="invalid-feedback porsi-feedback">Minimal pemesanan 50 porsi.</div>
                                    @error('porsi_'.$menu->id)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-dark">Minimal pemesanan 50 porsi.</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">Belum ada menu pondokan yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Card 2: Menu Utama -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="fw-bold text-dark mb-0">MENU UTAMA (NASI LENGKAP)</h4>
                        <p class="text-muted small">Pilih jenis kemasan untuk masing-masing menu (Nasi Kotak atau Prasmanan).</p>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @forelse($menuUtama as $menu)
                            @php
                                $detail = isset($pesanan) ? $pesanan->detailPesanans->firstWhere('menu_id', $menu->id) : null;
                                $porsiValue = $detail ? $detail->porsi : '';
                                $tipeValue = $detail ? $detail->tipe_penyajian : '';
                            @endphp
                            <div class="menu-section mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" data-menu-id="{{ $menu->id }}">
                                <h5 class="fw-bold text-dark mb-1">{{ $menu->nama_menu }}</h5>
                                <p class="text-dark small mb-3">
                                    {{ $menu->deskripsi }}
                                    @if($menu->harga > 0)
                                        <br><strong>Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong> / porsi
                                    @endif
                                </p>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Penyajian</label>
                                    <select name="tipe_penyajian_{{ $menu->id }}" class="form-select penyajian-input" style="max-width: 300px;" required>
                                        <option value="">-- Pilih Kemasan --</option>
                                        <option value="Nasi Kotak" {{ $tipeValue == 'Nasi Kotak' ? 'selected' : '' }}>Nasi Kotak</option>
                                        <option value="Prasmanan" {{ $tipeValue == 'Prasmanan' ? 'selected' : '' }}>Prasmanan</option>
                                    </select>
                                    <div class="invalid-feedback penyajian-feedback">Pilih jenis penyajian.</div>
                                </div>

                                <div class="mb-2">
                                    <label for="porsi_{{ $menu->id }}" class="form-label fw-semibold text-dark">Jumlah Porsi</label>
                                    <input type="number" class="form-control porsi-input @error('porsi_'.$menu->id) is-invalid @enderror" style="max-width: 300px;" name="porsi_{{ $menu->id }}" id="porsi_{{ $menu->id }}" value="{{ $porsiValue }}">
                                    <div class="invalid-feedback porsi-feedback">Minimal pemesanan 50 porsi.</div>
                                    @error('porsi_'.$menu->id)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-dark">Minimal pemesanan 50 porsi.</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">Belum ada menu utama yang tersedia.</p>
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
                <div class="d-flex justify-content-end gap-2 mb-5">
                    @auth
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
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
                document.querySelectorAll('.items-feedback, .minuman-feedback, .jumlah-cup-feedback, .penyajian-feedback').forEach(el => {
                    el.classList.remove('d-block');
                    el.classList.add('d-none');
                });

                document.querySelectorAll('.menu-section').forEach(section => {
                    const porsiInput = section.querySelector('.porsi-input');
                    const penyajianInput = section.querySelector('.penyajian-input');
                    
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

                        // Check penyajian if it exists
                        if (penyajianInput && penyajianInput.value === "") {
                            penyajianInput.classList.add('is-invalid');
                            const penyajianFeedback = section.querySelector('.penyajian-feedback');
                            if (penyajianFeedback) {
                                penyajianFeedback.classList.remove('d-none');
                                penyajianFeedback.classList.add('d-block');
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
