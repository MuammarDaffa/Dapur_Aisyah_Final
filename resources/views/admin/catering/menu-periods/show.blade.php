@extends('layouts.admin')
@section('title', 'Detail Periode: ' . $period->formatted_range)
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.catering.show', $catering) }}" class="text-sm text-gray-500 hover:text-orange-500 transition-colors">← Kembali ke Detail Katering</a>
            <h2 class="text-xl font-bold text-gray-900 mt-1">📅 Periode: {{ $period->formatted_range }}</h2>
        </div>
        <div class="flex items-center gap-2">
            @if($period->isCurrent())
                <span class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">🟢 Periode Aktif Sekarang</span>
            @endif
            <button type="button" onclick="document.getElementById('editPeriodModal').style.display='flex'"
                class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                ✏️ Edit Periode
            </button>
        </div>
    </div>

    {{-- Assign Produk Form --}}
    @if(count($availableDates) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 mb-4">➕ Assign Produk ke Tanggal</h3>
        <form action="{{ route('admin.menu-periods.assign', [$catering, $period]) }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                <select name="menu_date" required class="w-full px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                    <option value="">Pilih Tanggal</option>
                    @foreach($availableDates as $date)
                        <option value="{{ $date->format('Y-m-d') }}">{{ $date->translatedFormat('l, d F Y') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Produk</label>
                <select name="product_id" required class="w-full px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} — Rp {{ number_format($product->price, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">Assign</button>
        </form>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <p class="text-sm text-green-700 font-medium">✅ Semua tanggal dalam periode ini sudah memiliki produk.</p>
    </div>
    @endif

    {{-- Menu Items Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-bold text-gray-800">🍽️ Daftar Menu per Tanggal</h3>
            <p class="text-xs text-gray-500 mt-0.5">1 tanggal = 1 produk utama</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Tanggal</th>
                    <th class="px-6 py-3 text-left font-semibold">Hari</th>
                    <th class="px-6 py-3 text-left font-semibold">Produk</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($period->items as $item)
                @php $isPast = $item->isPast(); @endphp
                <tr class="hover:bg-orange-50/30 transition-colors {{ $isPast ? 'opacity-50' : '' }}">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $item->menu_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">{{ $item->day_name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-8 h-8 rounded-lg object-cover">
                            @else
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-sm">🍽️</div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                @if($isPast)
                                <p class="text-xs text-red-500">Sudah lewat</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">{{ $item->product->formatted_price }}</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.menu-periods.remove', [$catering, $period, $item]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk dari tanggal ini?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <p class="text-3xl mb-2">🍽️</p>
                        <p class="font-medium">Belum ada produk yang di-assign.</p>
                        <p class="text-xs mt-1">Gunakan form di atas untuk assign produk ke tanggal.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Edit Periode --}}
<div id="editPeriodModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">✏️ Edit Periode</h3>
            <button onclick="document.getElementById('editPeriodModal').style.display='none'" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('admin.menu-periods.update', [$catering, $period]) }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" required value="{{ $period->start_date->format('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir *</label>
                    <input type="date" name="end_date" required value="{{ $period->end_date->format('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $period->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editPeriodModal').style.display='none'" class="w-full px-6 py-2.5 text-gray-700 bg-gray-100 font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button type="submit" class="w-full px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Update Periode</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('editPeriodModal')?.addEventListener('click', function(e) { if (e.target === this) this.style.display='none'; });
</script>
@endpush
@endsection
