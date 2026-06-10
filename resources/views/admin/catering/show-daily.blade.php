@extends('layouts.admin')
@section('title', 'Detail Katering: ' . $catering->name)
@section('content')
<div class="space-y-6">
    {{-- Back Link --}}
    <a href="{{ route('admin.catering.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-orange-500 transition-colors">
        ← Kembali ke Daftar Katering
    </a>

    {{-- Header Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div class="flex items-start gap-4">
                    @if($catering->image)
                    <img src="{{ asset('storage/' . $catering->image) }}" alt="{{ $catering->name }}" class="w-20 h-20 rounded-xl object-cover border">
                    @else
                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center text-3xl">🍲</div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $catering->name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">📦 Daily</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $catering->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $catering->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        @if($catering->description)
                        <p class="text-sm text-gray-600 mt-2 max-w-lg">{{ $catering->description }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.catering.edit', $catering) }}" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    ✏️ Edit Katering
                </a>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-4 border-t">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Harga Mulai</p>
                    <p class="text-lg font-bold text-orange-600 mt-1">Rp {{ number_format($catering->base_price, 0, ',', '.') }}</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Min. Porsi</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->min_portion }}</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Max. Porsi</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->max_portion ?? '∞' }}</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Produk</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->products()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Produk Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">🍽️ Produk</h3>
                <p class="text-xs text-gray-500 mt-0.5">Produk menu harian untuk katering ini</p>
            </div>
            <a href="{{ route('admin.products.create') }}?catering_service_id={{ $catering->id }}"
               class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Produk
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Produk</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Best Seller</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $p)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($p->image)
                            <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-lg">🍽️</div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $p->name }}</p>
                                @if($p->available_days)
                                <p class="text-xs text-gray-400 mt-0.5">{{ implode(', ', array_map('ucfirst', $p->available_days)) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">{{ $p->formatted_price }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($p->is_best_seller)<span class="text-red-500">🔥</span>@else<span class="text-gray-300">—</span>@endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.products.edit', $p) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">🍽️</p>
                        <p class="text-sm">Belum ada produk untuk katering ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
        <div class="px-6 py-3 border-t">{{ $products->links() }}</div>
        @endif
    </div>

    {{-- Extra Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">✨ Extra Tersedia</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Extra yang dapat dipesan pelanggan untuk produk daily ini</p>
                </div>
                <a href="{{ route('admin.custom-options.create') }}" class="text-xs text-orange-500 hover:text-orange-600 font-medium">
                    Kelola di Custom Options →
                </a>
            </div>
        </div>
        <div class="p-6">
            @if($extras->count() > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($extras as $extra)
                <div class="inline-flex items-center gap-2 px-3 py-2 bg-orange-50 border border-orange-200 rounded-lg">
                    <span class="text-sm font-medium text-orange-700">{{ $extra->name }}</span>
                    <span class="text-xs text-orange-500">Rp {{ number_format($extra->price, 0, ',', '.') }}</span>
                    @if(!$extra->is_active)
                    <span class="px-1.5 py-0.5 text-[10px] rounded bg-gray-200 text-gray-500">Nonaktif</span>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4 text-gray-400">
                <p class="text-2xl mb-1">✨</p>
                <p class="text-sm">Belum ada extra untuk katering ini.</p>
                <p class="text-xs mt-1">Tambahkan extra melalui menu <a href="{{ route('admin.custom-options.create') }}" class="text-orange-500 hover:underline">Custom Options</a>.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
