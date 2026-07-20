@props(['produk'])

<div class="group bg-white rounded shadow-md overflow-hidden border border border-secondary hover:-translate-y-1">
    {{-- Produk Image --}}
    <div style="height: 192px;" class="position-relative overflow-hidden">
        @if($produk->image)
            <img src="{{ asset('storage/' . $produk->image) }}"
                 alt="{{ $produk->name }}"
                 class="w-100 h-100 object-cover group- transition-">
        @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                <span class="text-5xl">🍽️</span>
            </div>
        @endif

        {{-- Service Badge --}}
        @if($produk->layananKatering)
            <div class="position-absolute bg-white/90 backdrop-blur-sm text-secondary small fw-medium px-2.5 py-1 rounded-pill shadow">
                {{ $produk->layananKatering->name }}
            </div>
        @endif
    </div>

    {{-- Card Content --}}
    <div class="p-4 d-flex flex-column gap-2">
        <h3 class="fw-bold text-secondary fs-5 leading-tight line-clamp-1 group-hover:text-primary">
            {{ $produk->name }}
        </h3>

        @if($produk->deskripsi)
            <p class="fs-6 text-secondary line-clamp-2">{{ $produk->deskripsi }}</p>
        @endif

        {{-- Available Days --}}
        @if($produk->available_days)
            <div class="d-flex d-flex-wrap g-3">
                <span class="small bg-success text-white text-success px-2 py-0.5 rounded-pill fw-medium">
                    {{ ucfirst($produk->available_days) }}
                </span>
            </div>
        @endif

        {{-- Price & Action --}}
        <div class="d-flex align-items-center justify-content-between pt-2 border-t border border-secondary">
            <div>
                <span class="small text-secondary">Mulai dari</span>
                <p class="fs-5 fw-bold text-primary">
                    Rp {{ number_format($produk->harga, 0, ',', '.') }}
                </p>
            </div>

            <form action="{{ route('pelanggan.keranjang.store') }}" method="POST" onsubmit="return window.submitQuickAddCart ? window.submitQuickAddCart(event, this) : true;">
                @csrf
                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                <input type="hidden" name="jumlah" value="1">
                <button type="submit"
                        class="hover: hover: text-white p-2.5 rounded shadow-md hover:shadow active:"
                        title="Tambah ke Keranjang">
                    <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
