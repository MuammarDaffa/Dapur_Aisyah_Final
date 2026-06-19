@extends('layouts.admin')
@section('title', 'Menu Mingguan: ' . $catering->name)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.catering.show', $catering) }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors">← Kembali ke Detail Katering</a>
            <h2 class="text-xl font-bold text-gray-900 mt-1">📅 Menu Mingguan — <span class="text-orange-500">{{ $catering->name }}</span></h2>
        </div>
        <button type="button" onclick="document.getElementById('addPeriodModal').style.display='flex'"
            class="px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
            + Tambah Periode
        </button>
    </div>

    {{-- Periods Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Nama Periode</th>
                    <th class="px-6 py-3 text-center font-semibold">Tanggal Mulai</th>
                    <th class="px-6 py-3 text-center font-semibold">Tanggal Akhir</th>
                    <th class="px-6 py-3 text-center font-semibold">Jumlah Menu</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($periods as $period)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.menu-periods.show', [$catering, $period]) }}" class="font-medium text-gray-900 hover:text-orange-600 transition-colors">
                            {{ $period->nama_periode }}
                        </a>
                        @if($period->isCurrent())
                            <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif Sekarang</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600">{{ $period->start_date->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4 text-center text-gray-600">{{ $period->end_date->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $period->items_count }} menu</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $period->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $period->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.menu-periods.show', [$catering, $period]) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Detail</a>
                            <form action="{{ route('admin.menu-periods.destroy', [$catering, $period]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus periode ini beserta semua menu-nya?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <p class="text-3xl mb-2">📅</p>
                        <p class="font-medium">Belum ada periode menu.</p>
                        <p class="text-xs mt-1">Buat periode baru untuk mulai mengatur menu harian.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($periods->hasPages())
        <div class="px-6 py-3 border-t">{{ $periods->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal Tambah Periode --}}
<div id="addPeriodModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">📅 Tambah Periode Menu</h3>
            <button onclick="document.getElementById('addPeriodModal').style.display='none'" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('admin.menu-periods.store', $catering) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Periode *</label>
                <input type="text" name="nama_periode" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500" placeholder="cth: Menu Minggu Ke-3 Juli 2026">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir *</label>
                    <input type="date" name="end_date" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
            <button type="submit" class="w-full px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Buat Periode</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('addPeriodModal')?.addEventListener('click', function(e) { if (e.target === this) this.style.display='none'; });
</script>
@endpush
@endsection
