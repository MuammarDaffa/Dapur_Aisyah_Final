@extends('layouts.app')
@section('title', 'Pilih Menu Acara')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <h2 class="fs-3 fw-bold text-dark  mt-2 mb-1">Pilih Menu untuk {{ $layanan->nama }}</h2>
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

            <form action="{{ route('pelanggan.acara.simpan_menu', $pesanan->id) }}" method="POST" id="formPilihMenu">
                @csrf
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        @forelse($menus as $menu)
                            <div class="menu-section" data-menu-id="{{ $menu->id }}">
                                <h5 class="fw-bold text-dark mb-1">{{ $menu->nama_menu }}</h5>
                                <p class="text-dark small mb-3">
                                    {{ $menu->deskripsi }}
                                    @if($menu->harga > 0)
                                        &bull; <strong>Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong> / porsi dasar
                                    @endif
                                </p>
                                
                                @if($menu->items->count() > 0)
                                    <div class="mb-3">
                                        @foreach($menu->items as $item)
                                            <div class="form-check mb-2 d-flex justify-content-between align-items-center" style="max-width: 500px;">
                                                <div>
                                                    <input class="form-check-input me-2" type="checkbox" name="items_{{ $menu->id }}[]" value="{{ $item->id }}" id="item_{{ $item->id }}">
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
                                    </div>
                                @else
                                    <div class="text-dark small mb-3 font-italic">
                                        Belum ada pilihan item untuk menu ini.
                                    </div>
                                @endif

                                <div class="mb-2">
                                    <label for="porsi_{{ $menu->id }}" class="form-label fw-semibold text-dark">Jumlah Porsi</label>
                                    <input type="number" class="form-control porsi-input" style="max-width: 300px;" name="porsi_{{ $menu->id }}" id="porsi_{{ $menu->id }}" min="50" >
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

                @if($menus->count() > 0)
                    <div class="d-flex justify-content-end mb-5">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                            Lanjut
                        </button>
                    </div>
                @endif
                <div class="d-flex justify-content-start mb-5">
                    <a href="{{ route('pelanggan.acara.service', ['service' => $pesanan->layanan_id, 'pesanan_id' => $pesanan->id]) }}"
                       class="btn btn-secondary px-5 py-2 fw-bold shadow-sm">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formPilihMenu');
        
        // Buat hidden input untuk menu_id
        const hiddenMenuId = document.createElement('input');
        hiddenMenuId.type = 'hidden';
        hiddenMenuId.name = 'menu_id';
        hiddenMenuId.id = 'hidden_menu_id';
        form.appendChild(hiddenMenuId);

        const menuCards = document.querySelectorAll('.menu-section');
        
        menuCards.forEach(card => {
            // Ketika ada interaksi (ketik/centang) di dalam card
            card.addEventListener('input', function() {
                const menuId = this.dataset.menuId;
                hiddenMenuId.value = menuId;
                
                // Hilangkan required di semua porsi
                document.querySelectorAll('.porsi-input').forEach(input => {
                    input.required = false;
                });
                
                // Jadikan porsi di card ini wajib diisi
                const activePorsi = document.getElementById('porsi_' + menuId);
                if(activePorsi) {
                    activePorsi.required = true;
                }
            });
        });

        // Validasi saat submit jika belum ada menu yang diinteraksikan
        form.addEventListener('submit', function(e) {
            if (!hiddenMenuId.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan isi jumlah porsi pada salah satu menu terlebih dahulu.'
                });
            }
        });
    });
</script>
@endsection
