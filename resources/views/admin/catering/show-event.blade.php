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
                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center text-3xl">🎉</div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $catering->name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">🎉 Event</span>
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
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Paket</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->packages()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Paket Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">📋 Paket</h3>
                <p class="text-xs text-gray-500 mt-0.5">Paket catering untuk layanan event ini</p>
            </div>
            <a href="{{ route('admin.packages.create') }}?catering_service_id={{ $catering->id }}"
               class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Paket
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Nama Paket</th>
                    <th class="px-6 py-3 text-center font-semibold">Porsi</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Isi Paket</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($packages as $pkg)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $pkg->name }}</p>
                        @if($pkg->description)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $pkg->description }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-medium">{{ $pkg->total_portions }}</span>
                        <span class="text-xs text-gray-400">porsi</span>
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">
                        Rp {{ number_format($pkg->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($pkg->customOptions->count() > 0)
                        <div class="flex flex-wrap gap-1 justify-center">
                            @foreach($pkg->customOptions->take(3) as $opt)
                            <span class="px-1.5 py-0.5 text-[10px] rounded bg-gray-100 text-gray-600">{{ $opt->name }}</span>
                            @endforeach
                            @if($pkg->customOptions->count() > 3)
                            <span class="px-1.5 py-0.5 text-[10px] rounded bg-gray-200 text-gray-500">+{{ $pkg->customOptions->count() - 3 }}</span>
                            @endif
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $pkg->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.packages.edit', $pkg) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="inline" onsubmit="return confirm('Hapus paket ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">📋</p>
                        <p class="text-sm">Belum ada paket untuk katering ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($packages->hasPages())
        <div class="px-6 py-3 border-t">{{ $packages->links() }}</div>
        @endif
    </div>
</div>
@endsection
