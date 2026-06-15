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
                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center text-3xl">🍲</div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $catering->name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">📦 Daily</span>
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
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Produk</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $catering->products()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Produk Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">🍽️ Produk</h3>
                <p class="text-xs text-gray-500 mt-0.5">Produk menu harian untuk katering ini</p>
            </div>
            <a href="{{ route('admin.products.create') }}?catering_service_id={{ $catering->id }}"
               class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Produk
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Produk</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Best Seller</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Ketersediaan</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $p)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($p->image)
                            <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-lg">🍽️</div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $p->name }}</p>
                                @if($p->available_days)
                                <p class="text-xs text-gray-400 mt-0.5">{{ implode(', ', array_map('ucfirst', $p->available_days)) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">{{ $p->formatted_price }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($p->is_best_seller)<span class="text-red-500">🔥</span>@else<span class="text-gray-300">—</span>@endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ ($p->status ?? 'tersedia') === 'tersedia' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                            {{ ($p->status ?? 'tersedia') === 'tersedia' ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.products.edit', $p) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">🍽️</p>
                        <p class="text-sm">Belum ada produk untuk katering ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
        <div class="px-6 py-3 border-t">{{ $products->links() }}</div>
        @endif
    </div>

    {{-- Extra Section (Task 15 — table format with CRUD) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">✨ Extra</h3>
                <p class="text-xs text-gray-500 mt-0.5">Extra tambahan yang dapat dipesan pelanggan</p>
            </div>
            <button type="button" onclick="openExtraModal()" class="px-4 py-2 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                + Tambah Extra
            </button>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Nama</th>
                    <th class="px-6 py-3 text-center font-semibold">Harga</th>
                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                    <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($extras as $extra)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $extra->name }}</td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">Rp {{ number_format($extra->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $extra->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $extra->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" onclick="openEditExtraModal({{ $extra->id }}, '{{ $extra->name }}', {{ $extra->price }}, {{ $extra->is_active ? 'true' : 'false' }})" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</button>
                            <form action="{{ route('admin.catering.options.destroy', [$catering, $extra]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus extra ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                        <p class="text-2xl mb-1">✨</p>
                        <p class="text-sm">Belum ada extra. Contoh: Sambal Tambahan, Kerupuk, Air Mineral.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah Extra --}}
<div id="addExtraModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Tambah Extra</h3>
            <button onclick="document.getElementById('addExtraModal').style.display='none'" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('admin.catering.options.store', $catering) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="type" value="extra">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500" placeholder="cth: Sambal Tambahan">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                <input type="number" name="price" value="0" min="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </div>
            <button type="submit" class="w-full px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Simpan</button>
        </form>
    </div>
</div>

{{-- Modal Edit Extra --}}
<div id="editExtraModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Edit Extra</h3>
            <button onclick="document.getElementById('editExtraModal').style.display='none'" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editExtraForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" id="editExtraName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                <input type="number" name="price" id="editExtraPrice" value="0" min="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="editExtraActive" class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Aktif</span>
            </div>
            <button type="submit" class="w-full px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Update</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
const cateringId = {{ $catering->id }};

function openExtraModal() {
    document.getElementById('addExtraModal').style.display = 'flex';
}

function openEditExtraModal(id, name, price, isActive) {
    document.getElementById('editExtraName').value = name;
    document.getElementById('editExtraPrice').value = price;
    document.getElementById('editExtraActive').checked = isActive;
    document.getElementById('editExtraForm').action = `/admin/catering/${cateringId}/options/${id}`;
    document.getElementById('editExtraModal').style.display = 'flex';
}

document.getElementById('addExtraModal')?.addEventListener('click', function(e) { if (e.target === this) this.style.display='none'; });
document.getElementById('editExtraModal')?.addEventListener('click', function(e) { if (e.target === this) this.style.display='none'; });
</script>
@endpush
@endsection
