@extends('layouts.app')
@section('title', 'Pilih Menu Acara')

@section('content')
<!-- memanggil CSS leaflet dari CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<div class="container mx-auto px-4 py-8 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <h2 class="fs-3 fw-bold text-dark mt-2 mb-1">Pilih Menu untuk {{ $service->nama }}</h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
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

            <form action="{{ route('pelanggan.acara.simpan_menu') }}" method="POST" id="formPilihMenu">
                @csrf
                <input type="hidden" name="layanan_id" value="{{ $service->id }}">
                @if(isset($pesanan))
                    <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">
                @endif
                <input type="hidden" name="latitude" id="input_latitude" value="{{ isset($pesanan) ? $pesanan->latitude : '' }}">
                <input type="hidden" name="longitude" id="input_longitude" value="{{ isset($pesanan) ? $pesanan->longitude : '' }}">
                
                <!-- Pilihan Radio Button -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-black">Pilih Metode Pengambilan:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_ambil" value="ambil_sendiri" {{ (!isset($pesanan) || $pesanan->metode_pengambilan == 'ambil_sendiri') ? 'checked' : '' }}>
                                <label class="form-check-label text-black" for="radio_ambil">
                                    Ambil Sendiri 
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_pengambilan" id="radio_antar" value="diantar_ke_tempat" {{ (isset($pesanan) && $pesanan->metode_pengambilan == 'diantar_ke_tempat') ? 'checked' : '' }}>
                                <label class="form-check-label text-black" for="radio_antar">
                                    Di Antar ke Lokasi  
                                </label>
                            </div>
                        </div>

                        <!-- Input Datepicker -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-black">Pilih Tanggal Acara</label>
                            <input type="date" name="tanggal_acara" id="tanggal_acara" class="form-control border-danger" required value="{{ isset($pesanan) && $pesanan->tanggal_pesanan ? \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('Y-m-d') : '' }}">
                        </div>

                        <!-- Wadah Peta -->
                        <div id="wadah_peta" style="display: none;">
                            <hr class="my-4">
                            <h5 class="fw-bold mb-3">Tentukan Lokasi Pengantaran</h5>
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i>*Silakan klik pada peta untuk memilih lokasi pengantaran</i></span>
                            </div>
                            <div id="map" class="w-100 rounded border border-2 shadow-sm" style="height: 50vh; min-height: 400px; z-index: 1;"></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        @forelse($menus as $menu)
                            @php
                                $detail = isset($pesanan) ? $pesanan->detailPesanans->firstWhere('menu_id', $menu->id) : null;
                                $porsiValue = $detail ? $detail->porsi : '';
                                $selectedItems = $detail ? $detail->menuItems->pluck('id')->toArray() : [];
                            @endphp
                            <div class="menu-section" data-menu-id="{{ $menu->id }}">
                                <h5 class="fw-bold text-dark mb-1">{{ $menu->nama_menu }}</h5>
                                <p class="text-dark small mb-3">
                                    {{ $menu->deskripsi }}
                                    @if($menu->harga > 0)
                                        &bull; <strong>Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong> / porsi dasar
                                    @endif
                                </p>
                                
                                @if($menu->items->count() > 0)
                                    <div class="mb-3 checkbox-group">
                                        @foreach($menu->items as $item)
                                            <div class="form-check mb-2 d-flex justify-content-between align-items-center" style="max-width: 500px;">
                                                <div>
                                                    <input class="form-check-input me-2 item-checkbox" type="checkbox" name="items_{{ $menu->id }}[]" value="{{ $item->id }}" id="item_{{ $item->id }}" {{ in_array($item->id, $selectedItems) ? 'checked' : '' }}>
                                                    <label class="form-check-label text-dark" style="cursor: pointer;" for="item_{{ $item->id }}">
                                                        {{ $item->nama }}
                                                    </label>
                                                </div>
                                                <span class="text-dark small">
                                                    @if($item->harga > 0)
                                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                                    @else
                                                        <span class="text-success fw-semibold">Gratis</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                        <div class="invalid-feedback items-feedback d-none">Pilih minimal satu item menu.</div>
                                        @error('items_'.$menu->id)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <div class="text-dark small mb-3 font-italic">
                                        Belum ada pilihan item untuk menu ini.
                                    </div>
                                @endif

                                <div class="mb-2">
                                    <label for="porsi_{{ $menu->id }}" class="form-label fw-semibold text-dark">Jumlah Porsi</label>
                                    <input type="number" class="form-control porsi-input @error('porsi_'.$menu->id) is-invalid @enderror" style="max-width: 300px;" name="porsi_{{ $menu->id }}" id="porsi_{{ $menu->id }}" value="{{ $porsiValue }}">
                                    <div class="invalid-feedback porsi-feedback">Jumlah porsi wajib diisi.</div>
                                    @error('porsi_'.$menu->id)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-dark">Minimal pemesanan 50 porsi.</div>
                                </div>

                                @if(!$loop->last)
                                    <hr class="my-4 border-secondary opacity-25">
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <p class="text-dark mb-0">Belum ada menu yang tersedia untuk layanan ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if(isset($minumans) && $minumans->count() > 0)
                <!-- Bagian Minuman -->
                <div class="card shadow-sm border-0 mb-4" id="minuman_section">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-1">Pilihan Minuman</h5>
                        <p class="text-dark small mb-3">
                            Minuman bersifat opsional. Tidak dihitung ke dalam kuota mingguan.
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
                                    } elseif (isset($pesanan) && $pesanan->detailPesananMinumans) {
                                        $detailMinuman = $pesanan->detailPesananMinumans->where('minuman_id', $minuman->id)->first();
                                        if($detailMinuman) {
                                            $isChecked = true;
                                            $minumanValue = $detailMinuman->jumlah;
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

                @if($menus->count() > 0)
                    <!-- Tipe Penyajian -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-3">Tipe Penyajian</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="tipe_penyajian" id="tipe_nasi_kotak" value="Nasi Kotak" {{ (!isset($pesanan) || $pesanan->tipe_penyajian == 'nasi_kotak') ? 'checked' : '' }} required>
                                <label class="form-check-label text-dark" for="tipe_nasi_kotak">
                                    Nasi Kotak
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipe_penyajian" id="tipe_prasmanan" value="Prasmanan" {{ (isset($pesanan) && $pesanan->tipe_penyajian == 'prasmanan') ? 'checked' : '' }} required>
                                <label class="form-check-label text-dark" for="tipe_prasmanan">
                                    Prasmanan
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-5">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                            Buat Pesanan
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Memanggil javascript leaflet dari CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formPilihMenu');
        
        var initLat = {{ isset($pesanan) && $pesanan->latitude ? $pesanan->latitude : -0.03194 }};
        var initLng = {{ isset($pesanan) && $pesanan->longitude ? $pesanan->longitude : 109.325 }};
        var map = L.map('map').setView([initLat, initLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([initLat, initLng])
                      .addTo(map)
                      .bindPopup("<b>Halo!</b><br>Pesanan akan diantar kesini.");

        map.on('click', function(e){
            var lokasiBaru = e.latlng;
            marker.setLatLng(lokasiBaru);
            document.getElementById('input_latitude').value = lokasiBaru.lat;
            document.getElementById('input_longitude').value = lokasiBaru.lng;
        }); 

        const radioAmbil = document.getElementById('radio_ambil');
        const radioAntar = document.getElementById('radio_antar');
        const wadahPeta = document.getElementById('wadah_peta');

        function aturTampilanPeta() {
            if (radioAntar.checked) {
                wadahPeta.style.display = 'block';
                setTimeout(function(){ 
                    map.invalidateSize(); 
                }, 100);
            } else {
                wadahPeta.style.display = 'none';
            }
        }

        aturTampilanPeta();

        radioAmbil.addEventListener('change', aturTampilanPeta);
        radioAntar.addEventListener('change', aturTampilanPeta);

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
                const checkboxes = section.querySelectorAll('.item-checkbox');
                
                let hasPorsi = porsiInput.value && parseInt(porsiInput.value) > 0;
                let hasItems = false;
                
                checkboxes.forEach(cb => {
                    if (cb.checked) hasItems = true;
                });

                if (hasPorsi || hasItems) {
                    isAnyMenuSelected = true;
                }

                if (hasPorsi && !hasItems) {
                    // Show error on checkboxes
                    section.querySelector('.checkbox-group').classList.add('is-invalid');
                    const itemsFeedback = section.querySelector('.items-feedback');
                    if (itemsFeedback) {
                        itemsFeedback.classList.remove('d-none');
                        itemsFeedback.classList.add('d-block');
                    }
                    isValid = false;
                } else if (hasItems && !hasPorsi) {
                    // Show error on porsi
                    porsiInput.classList.add('is-invalid');
                    isValid = false;
                } else if (hasPorsi && hasItems) {
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
                    text: 'Terdapat kesalahan pada isian menu. Silakan periksa kembali pesan error yang muncul.'
                });
                return false;
            }

            if (!isAnyMenuSelected) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan pilih minimal satu menu.'
                });
                return false;
            }

            const lat = document.getElementById('input_latitude').value;
            const lng = document.getElementById('input_longitude').value;
            
            if (radioAntar.checked && (!lat || !lng)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Karena Anda memilih Di Antar ke Lokasi, silakan klik pada peta untuk menentukan lokasi pengantaran.'
                });
                return false;
            }
        });
    });
</script>
@endsection
