@props(['service'])

<div class="group bg-white rounded shadow-md overflow-hidden border border border-secondary hover:-translate-y-1">
    {{-- Service Image --}}
    <div class="position-relative overflow-hidden h-40">
        @if($service->image)
            <img src="{{ asset('storage/' . $service->image) }}"
                 alt="{{ $service->name }}"
                 class="w-100 h-100 object-cover group- transition-">
        @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                <span class="text-5xl">🏷️</span>
            </div>
        @endif

        {{-- Status Badge --}}
        @if($service->is_active)
            <div class="position-absolute bg-success text-white text-white small fw-bold px-2.5 py-1 rounded-pill shadow">
                Aktif
            </div>
        @else
            <div class="position-absolute bg-danger text-white text-white small fw-bold px-2.5 py-1 rounded-pill shadow">
                Nonaktif
            </div>
        @endif
    </div>

    {{-- Card Content --}}
    <div class="p-4 d-flex flex-column gap-2">
        <h3 class="fw-bold text-secondary fs-5 group-hover:text-success">
            {{ $service->name }}
        </h3>

        <p class="fs-6 text-secondary line-clamp-2">{{ $service->description }}</p>

        {{-- Serving Types --}}
        @if($service->serving_types)
            <div class="d-flex d-flex-wrap g-3.5">
                @foreach($service->serving_types as $type)
                    <span class="small bg-info text-white text-info px-2 py-0.5 rounded-pill fw-medium capitalize">
                        {{ $type }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Price & Min Portion --}}
        <div class="d-flex align-items-center justify-content-between pt-2 border-t border border-secondary">
            <div>
                <span class="small text-secondary">Harga dasar</span>
                <p class="fs-5 fw-bold text-success">
                    Rp {{ number_format($service->base_price, 0, ',', '.') }}
                </p>
            </div>
            <div class="text-end">
                <span class="small text-secondary">Min. porsi</span>
                <p class="fs-6 fw-bold text-secondary">{{ $service->min_portion }} porsi</p>
            </div>
        </div>
    </div>
</div>
