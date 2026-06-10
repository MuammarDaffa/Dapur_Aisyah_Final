@props(['service'])

<div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:-translate-y-1">
    {{-- Service Image --}}
    <div class="relative overflow-hidden h-40">
        @if($service->image)
            <img src="{{ asset('storage/' . $service->image) }}"
                 alt="{{ $service->name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-green-100 to-emerald-50 flex items-center justify-center">
                <span class="text-5xl">🏷️</span>
            </div>
        @endif

        {{-- Status Badge --}}
        @if($service->is_active)
            <div class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">
                Aktif
            </div>
        @else
            <div class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">
                Nonaktif
            </div>
        @endif
    </div>

    {{-- Card Content --}}
    <div class="p-4 space-y-3">
        <h3 class="font-semibold text-gray-800 text-lg group-hover:text-green-600 transition-colors">
            {{ $service->name }}
        </h3>

        <p class="text-sm text-gray-500 line-clamp-2">{{ $service->description }}</p>

        {{-- Serving Types --}}
        @if($service->serving_types)
            <div class="flex flex-wrap gap-1.5">
                @foreach($service->serving_types as $type)
                    <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-medium capitalize">
                        {{ $type }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Price & Min Portion --}}
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <div>
                <span class="text-xs text-gray-400">Harga dasar</span>
                <p class="text-lg font-bold text-green-600">
                    Rp {{ number_format($service->base_price, 0, ',', '.') }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs text-gray-400">Min. porsi</span>
                <p class="text-sm font-semibold text-gray-700">{{ $service->min_portion }} porsi</p>
            </div>
        </div>
    </div>
</div>
