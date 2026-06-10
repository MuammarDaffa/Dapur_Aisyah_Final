@extends('layouts.admin')

@section('title', 'Manajemen Ulasan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Ulasan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola ulasan dari pelanggan</p>
        </div>
        <div class="bg-gradient-to-r from-yellow-400 to-amber-500 text-white px-4 py-2 rounded-xl font-semibold shadow">
            ⭐ {{ $reviews->total() }} Ulasan
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Pelanggan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Layanan</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Rating</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Komentar</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $review->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $review->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700">{{ $review->order->cateringService->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="text-lg {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-600 max-w-xs truncate">{{ $review->comment ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                      onsubmit="event.preventDefault(); Swal.fire({ title: 'Hapus ulasan?', text: 'Ulasan ini akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Hapus', cancelButtonText: 'Batal' }).then((r) => { if(r.isConfirmed) this.submit(); })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all"
                                            title="Hapus Ulasan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-4xl">📝</span>
                                    <p class="text-gray-400 font-medium">Belum ada ulasan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
