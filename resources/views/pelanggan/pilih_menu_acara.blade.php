@extends('layouts.app')
@section('title', 'Pilih Menu Acara')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <a href="{{ route('pelanggan.dashboard') }}" class="text-decoration-none text-secondary">
                    &larr; Kembali
                </a>
                <h2 class="fs-3 fw-bold mt-2 mb-1">Pilih Menu untuk {{ $layanan->nama }}</h2>
                <p class="text-muted">Silakan pilih menu dan atur jumlah porsinya.</p>
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
                
                @forelse($menus as $menu)
                    <div class="card mb-4 shadow-sm border-0 border-top border-primary border-3 menu-card" data-menu-id="{{ $menu->id }}">
                        <div class="card-header bg-white pb-0 border-0 pt-4 px-4 d-flex align-items-center">
                            <div>
                                <h4 class="card-title fw-bold mb-0">{{ $menu->nama_menu }}</h4>
                                <p class="text-muted small mb-0">{{ $menu->deskripsi }}</p>
                                @if($menu->harga > 0)
                                    <span class="badge bg-primary mt-2">Rp {{ number_format($menu->harga, 0, ',', '.') }} / porsi dasar</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-4">
                            
                            @if($menu->items->count() > 0)
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Pilihan Item:</h6>
                                    <ul class="list-group list-group-flush border rounded">
                                        @foreach($menu->items as $item)
                                            <li class="list-group-item px-3 d-flex justify-content-between align-items-center py-2">
                                                <div class="form-check w-100 d-flex align-items-center mb-0">
                                                    <input class="form-check-input me-3" type="checkbox" name="items_{{ $menu->id }}[]" value="{{ $item->id }}" id="item_{{ $item->id }}">
                                                    <label class="form-check-label w-100 d-flex justify-content-between align-items-center mb-0" style="cursor: pointer;" for="item_{{ $item->id }}">
                                                        <span>{{ $item->nama }}</span>
                                                        <span class="text-muted">
                                                            @if($item->harga > 0)
                                                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                                                            @else
                                                                <span class="text-success small">Gratis</span>
                                                            @endif
                                                        </span>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-light border text-center text-muted">
                                    Belum ada pilihan item untuk menu ini.
                                </div>
                            @endif

                            <div class="mb-4">
                                <label for="porsi_{{ $menu->id }}" class="form-label fw-bold">Jumlah Porsi</label>
                                <input type="number" class="form-control form-control-lg porsi-input" name="porsi_{{ $menu->id }}" id="porsi_{{ $menu->id }}" min="50" placeholder="Contoh: 150">
                                <div class="form-text text-muted">Minimal pemesanan 50 porsi.</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card shadow-sm border-0 text-center py-5">
                        <div class="card-body">
                            <p class="text-muted mb-0">Belum ada menu yang tersedia untuk layanan ini.</p>
                        </div>
                    </div>
                @endforelse

                @if($menus->count() > 0)
                    <div class="d-flex justify-content-end mb-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow">
                            Simpan & Lanjut ke Pembayaran
                        </button>
                    </div>
                @endif
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

        const menuCards = document.querySelectorAll('.menu-card');
        
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
