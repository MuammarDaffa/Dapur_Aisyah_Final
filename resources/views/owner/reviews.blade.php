@extends('layouts.owner')

@section('title', 'Ulasan Pelanggan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Ulasan Pelanggan</h2>
            <p class="text-sm text-gray-500 mt-1">Lihat semua ulasan dari pelanggan</p>
        </div>
        <div class="bg-gradient-to-r from-yellow-400 to-amber-500 text-white px-4 py-2 rounded-xl font-semibold shadow flex items-center gap-2">
            <span>⭐</span>
            <span>{{ $reviews->total() }} Ulasan</span>
        </div>
    </div>

    {{-- Reviews Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($reviews as $review)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-shadow">
                {{-- Header --}}
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $review->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $review->created_at->format('d M Y · H:i') }}</p>
                    </div>
                </div>

                {{-- Rating --}}
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="text-xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                    @endfor
                    <span class="ml-2 text-sm font-medium text-gray-500">{{ $review->rating }}/5</span>
                </div>

                {{-- Comment --}}
                @if($review->comment)
                    <p class="text-gray-600 text-sm leading-relaxed bg-gray-50 rounded-lg p-3 mt-2">
                        "{{ $review->comment }}"
                    </p>
                @else
                    <p class="text-gray-400 text-sm italic mt-2">Tanpa komentar</p>
                @endif

                {{-- Service Info --}}
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
                    <span class="text-xs text-gray-400">Layanan:</span>
                    <span class="text-xs font-medium text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full">
                        {{ $review->order->cateringService->name ?? '-' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <span class="text-5xl block mb-3">📝</span>
                <p class="text-gray-400 font-medium text-lg">Belum ada ulasan</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
        <div class="flex justify-center">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
