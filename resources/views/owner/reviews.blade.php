@extends('layouts.owner')

@section('title', 'Ulasan Pelanggan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Ulasan Pelanggan</h2>
            <p class="fs-6 text-secondary mt-1">Lihat semua ulasan dari pelanggan</p>
        </div>
        <div class="text-white px-4 py-2 rounded fw-bold shadow d-flex align-items-center g-3">
            <span>⭐</span>
            <span>{{ $ulasan->total() }} Ulasan</span>
        </div>
    </div>

    {{-- Ulasan Grid --}}
    <div class="row row-cols-1 md:row-cols-2 g-3">
        @forelse($ulasan as $ulasan)
            <div class="bg-white rounded shadow-md border border border-secondary p-5 hover:shadow transition-shadow">
                {{-- Header --}}
                <div class="d-flex align-items-center g-3 mb-3">
                    <div style="height: 40px;" class="w-10 rounded-pill d-flex align-items-center justify-content-center text-white fs-6 fw-bold">
                        {{ strtoupper(substr($ulasan->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="d-flex-1">
                        <p class="fw-bold text-secondary">{{ $ulasan->user->name ?? '-' }}</p>
                        <p class="small text-secondary">{{ $ulasan->created_at->format('d M Y · H:i') }}</p>
                    </div>
                </div>

                {{-- Comment --}}
                @if($ulasan->comment)
                    <p class="text-secondary fs-6 leading-relaxed bg-light rounded p-3 mt-2">
                        "{{ $ulasan->comment }}"
                    </p>
                @else
                    <p class="text-secondary fs-6 italic mt-2">Tanpa komentar</p>
                @endif

                {{-- Service Info --}}
                <div class="mt-3 pt-3 border-t border border-secondary d-flex align-items-center g-3">
                    <span class="small text-secondary">Layanan:</span>
                    <span class="small fw-medium text-teal-600 bg-teal-50 px-2 py-0.5 rounded-pill">
                        {{ $ulasan->pesanan->layananKatering->name ?? '-' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <span class="text-5xl d-block mb-3">📝</span>
                <p class="text-secondary fw-medium fs-5">Belum ada ulasan</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($ulasan->hasPages())
        <div class="d-flex justify-content-center">
            {{ $ulasan->links() }}
        </div>
    @endif
</div>
@endsection
