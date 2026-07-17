@extends('layouts.admin')
@section('title', 'Kelola Katering')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Katering</span>
            </h3>
            <p class="text-sm text-gray-500 mt-1">Kelola semua layanan katering Anda</p>
        </div>
        <a href="{{ route('admin.catering.create') }}" class="px-5 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
            + Tambah Katering
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 items-center">
        <div class="flex gap-2">
            <a href="{{ route('admin.catering.index') }}"
               class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ !request('type') ? 'bg-gray-800 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.catering.index', ['type' => 'daily']) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request('type') === 'daily' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span>Daily</span>
            </a>
            <a href="{{ route('admin.catering.index', ['type' => 'event']) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request('type') === 'event' ? 'bg-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                <span>Event</span>
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3.5 text-left font-semibold">Nama Katering</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Tipe</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Harga Mulai</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Status</th>
                    <th class="px-6 py-3.5 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($caterings as $c)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $c->name }}</p>
                        @if($c->description)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ Str::limit($c->description, 60) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($c->isDaily())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <span>Daily</span>
                            </span>
                        @elseif($c->isEvent())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                <span>Event</span>
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">
                        Rp {{ number_format($c->base_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $c->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.catering.show', $c) }}" class="px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">Detail</a>
                            <a href="{{ route('admin.catering.edit', $c) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form action="{{ route('admin.catering.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus katering ini beserta semua data terkait?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <p class="font-medium">Belum ada katering.</p>
                        <p class="text-xs mt-1">Buat layanan katering pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $caterings->withQueryString()->links() }}</div>
</div>
@endsection
