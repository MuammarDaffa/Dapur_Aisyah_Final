@extends('layouts.admin')
@section('title', 'Detail Pelanggan')
@section('content')
<a href="{{ route('admin.customers') }}" class="text-sm text-orange-500 mb-4 inline-block">← Kembali</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Info Pelanggan --}}
    <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
            <div>
                <h3 class="font-bold text-lg">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <p class="text-sm text-gray-500 inline-flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>{{ $user->phone ?? '-' }}</span>
                </p>
                @if($user->old_phone)
                    <p class="text-xs text-gray-400 mt-1">HP Lama: <span class="line-through">{{ $user->old_phone }}</span></p>
                @endif
                <p class="text-sm text-gray-500 mt-2">Bergabung: {{ $user->created_at->format('d M Y') }}</p>
            </div>
            <div>
                @if($user->status_suspend === 'suspended')
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 text-sm rounded-full font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>
                        <span>Ditangguhkan</span>
                    </span>
                @elseif($user->status_suspend === 'pending_verification')
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-100 text-yellow-700 text-sm rounded-full font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Menunggu Verifikasi</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 text-sm rounded-full font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Aktif</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Aksi Akun --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border">
        <h4 class="font-bold text-sm mb-4">Kelola Akun</h4>
        <div class="space-y-3">
            @if($user->status_suspend === 'active')
                <form action="{{ route('admin.customers.suspend', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="button" onclick="confirmSuspend(this.closest('form'))" class="w-full px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>
                        <span>Tangguhkan Akun</span>
                    </button>
                </form>
            @elseif($user->status_suspend === 'suspended')
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    Akun ini ditangguhkan. Pelanggan tidak bisa melakukan checkout.
                </div>
                <form action="{{ route('admin.customers.activate', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Aktifkan Kembali</span>
                    </button>
                </form>
            @elseif($user->status_suspend === 'pending_verification')
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                    <p class="font-medium mb-1">Perubahan Nomor HP Terdeteksi</p>
                    <p>HP Lama: <span class="line-through">{{ $user->old_phone }}</span></p>
                    <p>HP Baru: <span class="font-bold">{{ $user->phone }}</span></p>
                </div>
                <form action="{{ route('admin.customers.activate', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Verifikasi & Aktifkan</span>
                    </button>
                </form>
                <form action="{{ route('admin.customers.suspend', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="button" onclick="confirmSuspend(this.closest('form'))" class="w-full px-4 py-2.5 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>
                        <span>Tetap Tangguhkan</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- Riwayat Pesanan --}}
<h3 class="font-bold mb-4">Riwayat Pesanan</h3>
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[600px]">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
                <th class="px-6 py-3 text-left">Order</th>
                <th class="px-6 py-3 text-center">Layanan</th>
                <th class="px-6 py-3 text-center">Total</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $o)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4"><a href="{{ route('admin.orders.show', $o) }}" class="text-orange-500 font-medium">{{ $o->order_number }}</a></td>
                <td class="px-6 py-4 text-center">{{ $o->cateringService->name ?? '-' }}</td>
                <td class="px-6 py-4 text-center">Rp {{ number_format($o->total,0,',','.') }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ match($o->status) { 'completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700','processing'=>'bg-blue-100 text-blue-700','on_delivery'=>'bg-purple-100 text-purple-700',default=>'bg-gray-100 text-gray-700' } }}">{{ $o->status_label }}</span>
                </td>
                <td class="px-6 py-4 text-center">{{ $o->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links() }}</div>

@push('scripts')
<script>
function confirmSuspend(form) {
    Swal.fire({
        title: 'Tangguhkan Akun?',
        text: 'Akun pelanggan ini akan ditangguhkan. Mereka tidak bisa melakukan checkout sampai diaktifkan kembali.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Tangguhkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
