@extends('layouts.admin')
@section('title', 'Kelola Katering')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="d-flex d-flex-column sm:d-flex-row justify-content-between items-start sm:align-items-center g-3">
        <div>
            <h3 class="fs-5 fw-bold text-secondary d-flex align-items-center g-3">
                <svg style="width: 20px; height: 20px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Katering</span>
            </h3>
            <p class="fs-6 text-secondary mt-1">Kelola semua layanan katering Anda</p>
        </div>
        <a href="{{ route('admin.catering.create') }}" class="px-5 py-2.5 bg-primary text-white text-white fs-6 fw-medium rounded hover:bg-primary text-white shadow-sm">
            + Tambah Katering
        </a>
    </div>

    {{-- Filters --}}
    <div class="d-flex d-flex-wrap g-3 align-items-center">
        <div class="d-flex g-3">
            <a href="{{ route('admin.catering.index') }}"
               class="px-4 py-2.5 rounded fs-6 fw-medium {{ !request('type') ? 'bg-light text-white shadow-sm' : 'bg-light text-secondary hover:bg-light' }}">
                Semua
            </a>
            <a href="{{ route('admin.catering.index', ['type' => 'daily']) }}"
               class="d-inline-d-flex align-items-center g-3.5 px-4 py-2.5 rounded fs-6 fw-medium {{ request('type') === 'daily' ? 'bg-info text-white text-white shadow-sm' : 'bg-light text-secondary hover:bg-light' }}">
                <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span>Daily</span>
            </a>
            <a href="{{ route('admin.catering.index', ['type' => 'event']) }}"
               class="d-inline-d-flex align-items-center g-3.5 px-4 py-2.5 rounded fs-6 fw-medium {{ request('type') === 'event' ? 'bg-purple-600 text-white shadow-sm' : 'bg-light text-secondary hover:bg-light' }}">
                <svg style="width: 16px; height: 16px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                <span>Event</span>
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded shadow-sm border border border-secondary overflow-hidden">
        <table class="w-100 fs-6">
            <thead class="bg-light">
                <tr class="small uppercase text-secondary tracking-wider">
                    <th class="px-6 py-3.5 text-start fw-bold">Nama Katering</th>
                    <th class="px-6 py-3.5 text-center fw-bold">Tipe</th>
                    <th class="px-6 py-3.5 text-center fw-bold">Harga Mulai</th>
                    <th class="px-6 py-3.5 text-center fw-bold">Status</th>
                    <th class="px-6 py-3.5 text-center fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($caterings as $c)
                <tr class="hover:bg-primary text-white/30">
                    <td class="px-6 py-4">
                        <p class="fw-medium text-secondary">{{ $c->name }}</p>
                        @if($c->description)
                        <p class="small text-secondary mt-0.5 line-clamp-1">{{ Str::limit($c->description, 60) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($c->isDaily())
                            <span class="d-inline-d-flex align-items-center g-3 px-2.5 py-1 rounded-pill small fw-medium bg-info text-white text-info">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <span>Daily</span>
                            </span>
                        @elseif($c->isEvent())
                            <span class="d-inline-d-flex align-items-center g-3 px-2.5 py-1 rounded-pill small fw-medium bg-purple-100 text-purple-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                <span>Event</span>
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-pill small fw-medium bg-light text-secondary">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center fw-bold text-primary">
                        Rp {{ number_format($c->base_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-pill small fw-medium {{ $c->is_active ? 'bg-success text-white text-success' : 'bg-light text-secondary' }}">
                            {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="d-flex align-items-center justify-content-center g-3">
                            <a href="{{ route('admin.catering.show', $c) }}" class="px-3 py-1.5 small fw-medium text-primary bg-primary text-white rounded hover:bg-primary text-white">Detail</a>
                            <a href="{{ route('admin.catering.edit', $c) }}" class="px-3 py-1.5 small fw-medium text-info bg-info text-white rounded hover:bg-info text-white">Edit</a>
                            <form action="{{ route('admin.catering.destroy', $c) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Hapus katering ini beserta semua data terkait?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn btn-danger px-3 py-1.5 small fw-medium text-danger bg-danger text-white rounded hover:bg-danger text-white">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-secondary">
                        <div style="width: 64px; height: 64px;" class="mx-auto mb-3 bg-light rounded-pill d-flex align-items-center justify-content-center">
                            <svg style="width: 32px; height: 32px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <p class="fw-medium">Belum ada katering.</p>
                        <p class="small mt-1">Buat layanan katering pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $caterings->withQueryString()->links() }}</div>
</div>
@endsection
