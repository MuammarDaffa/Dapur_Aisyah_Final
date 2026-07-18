@extends('layouts.admin')

@section('title', 'Manajemen Ulasan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Manajemen Ulasan</h2>
            <p class="fs-6 text-secondary mt-1">Kelola ulasan dari pelanggan</p>
        </div>
        <div class="text-white px-4 py-2 rounded fw-bold shadow d-inline-d-flex align-items-center g-3">
            <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            <span>{{ $reviews->total() }} Ulasan</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded shadow-md overflow-hidden border border border-secondary">
        <div class="overflow-x-auto">
            <table class="w-100 fs-6">
                <thead class="border-b border border-secondary">
                    <tr>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Pelanggan</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Layanan</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Komentar</th>
                        <th class="text-start px-6 py-4 fw-bold text-secondary">Tanggal</th>
                        <th class="text-center px-6 py-4 fw-bold text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-primary text-white/30">
                            <td class="px-6 py-4">
                                <div class="d-flex align-items-center g-3">
                                    <div style="height: 36px;" class="w-9 rounded-pill d-flex align-items-center justify-content-center text-white fs-6 fw-bold">
                                        {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="fw-medium text-secondary">{{ $review->user->name ?? '-' }}</p>
                                        <p class="small text-secondary">{{ $review->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-secondary">{{ $review->order->cateringService->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-secondary max-w-md">{{ $review->comment ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-secondary">{{ $review->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                      onsubmit="event.preventDefault(); Swal.fire({ title: 'Hapus ulasan?', text: 'Ulasan ini akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Hapus', cancelButtonText: 'Batal' }).then((r) => { if(r.isConfirmed) this.submit(); })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn btn-danger text-danger hover:text-danger hover:bg-danger text-white p-2 rounded"
                                            title="Hapus Ulasan">
                                        <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="d-flex d-flex-column align-items-center g-3">
                                    <div style="width: 64px; height: 64px;" class="bg-light rounded-pill d-flex align-items-center justify-content-center mb-1">
                                        <svg style="width: 32px; height: 32px;" class="text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    </div>
                                    <p class="text-secondary fw-medium">Belum ada ulasan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="px-6 py-4 border-t border border-secondary">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
