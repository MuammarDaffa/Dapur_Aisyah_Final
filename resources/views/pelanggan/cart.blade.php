@extends('layouts.app')
@section('title', 'Keranjang Belanja')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="fs-3 fw-bold text-secondary mb-6 d-flex align-items-center">
        <svg class="w-7 h-7 text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
        <span>Keranjang <span class="text-primary">Belanja</span></span>
    </h2>

    @php
        $packageGroupsCount = $grupAcara->filter(fn($items) => $items->firstWhere('item_type', 'package') !== null)->count();
        $customGroupsCount = $grupAcara->filter(fn($items) => $items->firstWhere('item_type', 'package') === null)->count();
        $totalEventBadge = $packageGroupsCount + $customGroupsCount;
    @endphp

    {{-- Tab Navigation --}}
    <div class="d-flex border-b border border-secondary mb-6">
        <button type="button" onclick="switchTab('harian')" id="tab-harian"
            class="px-6 py-3 fs-6 fw-bold border-b-2 d-flex align-items-center {{ $activeTab === 'harian' ? 'border border-primary text-primary' : 'border-transparent text-secondary hover:text-secondary' }}">
            <svg style="width: 16px; height: 16px;" class="me-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Harian</span>
            @if($grupHarian->isNotEmpty())
                <span class="ms-1 px-2 py-0.5 bg-primary text-white text-primary rounded-pill small fw-bold">{{ $grupHarian->flatten()->count() }}</span>
            @endif
        </button>
        <button type="button" onclick="switchTab('acara')" id="tab-acara"
            class="px-6 py-3 fs-6 fw-bold border-b-2 d-flex align-items-center {{ $activeTab === 'acara' ? 'border border-primary text-primary' : 'border-transparent text-secondary hover:text-secondary' }}">
            <svg style="width: 16px; height: 16px;" class="me-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            <span>Acara</span>
            @if($totalEventBadge > 0)
                <span class="ms-1 px-2 py-0.5 bg-purple-100 text-purple-600 rounded-pill small fw-bold">{{ $totalEventBadge }}</span>
            @endif
        </button>
    </div>

    {{-- =============================== --}}
    {{-- TAB DAILY --}}
    {{-- =============================== --}}
    <div id="content-harian" style="{{ $activeTab !== 'harian' ? 'display:none' : '' }}">
        @if($grupHarian->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border border-secondary">
                <!-- <div style="width: 64px; height: 64px;" class="mx-auto mb-4 text-secondary d-flex align-items-center justify-content-center">
                    <svg style="width: 48px; height: 48px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div> -->
                <p class="text-secondary fw-medium mb-4">Belum ada pesanan</p>
                <!-- <a href="{{ route('pelanggan.produk') }}" class="d-inline-d-flex align-items-center px-6 py-3 bg-primary text-white text-white fw-medium rounded-pill hover:bg-primary text-white">
                    <span>Lihat Menu</span>
                    <svg style="width: 16px; height: 16px;" class="ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a> -->
            </div>
        @else
            <div class="bg-white rounded shadow-sm border border border-primary p-6 space-y-6">
                @foreach($grupHarian->flatten() as $keranjang)
                @php
                    $basePrice = $keranjang->menuHarian ? (float) $keranjang->menuHarian->harga : ($keranjang->opsiKustom ? (float) $keranjang->opsiKustom->harga : 0);
                    $extrasList = collect();
                    $extrasPrice = 0;
                    if (!empty($keranjang->extras)) {
                        $extraIds = array_column($keranjang->extras, 'id');
                        $options = \App\Models\OpsiKustom::whereIn('id', $extraIds)->get()->keyBy('id');
                        foreach ($keranjang->extras as $extraData) {
                            if ($opt = $options->get($extraData['id'])) {
                                $exPrice = (float) $opt->harga * $extraData['qty'];
                                $extrasPrice += $exPrice;
                                $extrasList->push((object)[
                                    'name' => $opt->name,
                                    'qty' => $extraData['qty'],
                                    'harga' => $exPrice,
                                ]);
                            }
                        }
                    }
                @endphp
                <div class="d-flex items-start space-x-4 {{ !$loop->last ? 'border-b border border-secondary pb-6' : '' }}">
                    <div style="width: 64px; height: 64px;" class="bg-primary text-white rounded d-flex align-items-center justify-content-center fs-2 d-flex-flex-shrink-0">
                        @if($keranjang->menuHarian && null)
                            <img src="{{ Storage::url(null) }}" alt="{{ $keranjang->menuHarian->nama_menu }}" class="w-100 h-100 object-cover rounded">
                        @else
                            <svg style="width: 32px; height: 32px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        @endif
                    </div>
                    <div class="d-flex-1 w-100">
                        <div class="d-flex sm:items-start justify-content-between d-flex-column sm:d-flex-row g-3">
                            <div>
                                <h4 class="fw-bold text-secondary fs-5">{{ $keranjang->menuHarian->nama_menu ?? ($keranjang->opsiKustom->name ?? 'Item') }}</h4>
                                <p class="fs-6 fw-medium text-secondary">{{ $keranjang->jumlah }} Porsi</p>
                            </div>
                            <p class="fs-5 fw-bold text-secondary">Rp {{ number_format($keranjang->subtotal, 0, ',', '.') }}</p>
                        </div>
                        
                        @if($extrasList->isNotEmpty())
                            <div class="fs-6 text-secondary mt-2">
                                <span class="fw-medium text-secondary">Extra:</span> 
                                {{ collect($extrasList)->map(fn($ex) => $ex->name . ' ×' . $ex->qty)->implode(', ') }}
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-end mt-4 space-x-3">
                            <button type="button" onclick="openHarianEditModal({{ $keranjang->id }})" class="px-4 py-2 bg-primary text-white text-primary fs-6 fw-bold rounded hover:bg-primary text-white">Ubah Pesanan</button>
                            <form action="{{ route('pelanggan.keranjang.destroy', $keranjang) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn btn-danger px-4 py-2 bg-danger text-white text-danger fs-6 fw-bold rounded hover:bg-danger text-white">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tombol Lanjut Ke Pembayaran Global untuk Semua Daily -->
            <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border border-primary d-flex d-flex-column sm:d-flex-row justify-content-between align-items-center g-3 sticky-top">
                <div>
                    <h4 class="fw-bold text-secondary fs-5">Total Seluruh Pesanan Harian</h4>
                    <p class="fs-3 fw-bold text-primary">Rp {{ number_format($grupHarian->flatten()->sum('subtotal'), 0, ',', '.') }}</p>
                    <!-- <p class="fs-6 text-secondary mt-1">Satu kali checkout untuk seluruh menu harian.</p> -->
                </div>
                <a href="{{ route('pelanggan.checkout') }}"
                    class="w-100 sm:w-auto text-center px-8 py-4 text-white fw-bold rounded hover:shadow fs-5 d-flex align-items-center justify-content-center g-3">Lanjut Ke Pembayaran</a>
            </div>
        @endif
    </div>

    {{-- =============================== --}}
    {{-- TAB EVENT --}}
    {{-- =============================== --}}
    <div id="content-acara" style="{{ $activeTab !== 'acara' ? 'display:none' : '' }}">
        @if($grupAcara->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border border-secondary">
                <!-- <div style="width: 64px; height: 64px;" class="bg-light text-primary rounded-pill d-flex align-items-center justify-content-center mx-auto mb-4">
                    <svg style="width: 32px; height: 32px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div> -->
                <p class="text-secondary fw-medium mb-4">Belum ada pesanan </p>
                <!-- <a href="{{ route('landing') }}#services" class="d-inline-d-flex align-items-center px-6 py-3 bg-purple-500 text-white fw-medium rounded-pill">
                    <span>Pilih Layanan Acara</span>
                    <svg style="width: 16px; height: 16px;" class="ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a> -->
            </div>
        @else
            @php
                $packageGroups = $grupAcara->filter(fn($items) => $items->firstWhere('item_type', 'package') !== null);
                $customGroups = $grupAcara->filter(fn($items) => $items->firstWhere('item_type', 'package') === null);
            @endphp

            <div class="divide-y divide-gray-200 border-t border-b border border-secondary my-2">
                {{-- Kelompok 1: Paket Acara --}}
                @foreach($packageGroups as $groupId => $groupItems)
                @php
                    $packageItem = $groupItems->firstWhere('item_type', 'package');
                    $service = $groupItems->first()->layananKatering;
                    $groupSubtotal = $groupItems->sum(fn($c) => $c->subtotal);
                @endphp
                <div class="py-6 first:pt-4 last:pb-4 d-flex d-flex-column sm:d-flex-row sm:align-items-center justify-content-between g-3" id="acara-card-{{ $groupId }}">
                    <div class="d-flex-1 min-w-0 space-y-1">
                        <div class="d-flex d-flex-wrap align-items-center gap-x-2 gap-y-1">
                            <span class="fw-bold text-secondary text-base sm:fs-5">{{ $packageItem->paketKatering->name ?? 'Paket' }} ({{ $packageItem->jumlah }})</span>
                            <span class="text-secondary fw-medium">·</span>
                            <span class="fs-6 text-secondary fw-medium">{{ $service->name ?? 'Layanan Acara' }}</span>
                        </div>
                        <div class="pt-0.5">
                            <button type="button" onclick="openDetailModal('{{ $groupId }}')" class="small fw-bold text-purple-600 underline">Lihat Detail Menu</button>
                        </div>
                    </div>

                    <div class="d-flex d-flex-row sm:d-flex-column align-items-center sm:items-end justify-content-between sm:justify-content-center g-3.5 pt-2 sm:pt-0 border-t border border-secondary sm:border-t-0">
                        <p class="fs-5 fw-bold text-secondary sm:text-end">Rp {{ number_format($groupSubtotal, 0, ',', '.') }}</p>
                        <div class="d-flex align-items-center g-3 sm:justify-content-end">
                            <button type="button" onclick="confirmDeleteEvent('{{ $groupId }}', {{ $packageItem->id ?? $groupItems->first()->id }}, true)" class="px-2.5 py-1 rounded small fw-bold bg-danger text-white text-danger hover:bg-danger text-white">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Kelompok 2: Custom Menu --}}
                @foreach($customGroups as $groupId => $groupItems)
                @php
                    $customHeader = $groupItems->firstWhere('item_type', 'custom_header');
                    $menuItems = $groupItems->where('item_type', 'custom_menu');
                    $service = $groupItems->first()->layananKatering;
                    $groupSubtotal = $groupItems->sum(fn($c) => $c->subtotal);
                @endphp
                <div class="py-6 first:pt-4 last:pb-4 d-flex d-flex-column sm:d-flex-row sm:align-items-center justify-content-between g-3" id="acara-card-{{ $groupId }}">
                    <div class="d-flex-1 min-w-0 space-y-1">
                        <div class="d-flex d-flex-wrap align-items-center gap-x-2 gap-y-1">
                            <span class="fw-bold text-secondary text-base sm:fs-5">Custom Menu</span>
                            <span class="text-secondary fw-medium">·</span>
                            <span class="fs-6 text-secondary fw-medium">{{ $service->name ?? 'Layanan Acara' }}</span>
                        </div>
                        <p class="fs-6 fw-medium text-secondary">
                            {{ $menuItems->count() }} Menu Dipilih
                        </p>
                        <div class="pt-0.5">
                            <button type="button" onclick="openDetailModal('{{ $groupId }}')" class="small fw-bold text-purple-600 underline">Lihat Detail</button>
                        </div>
                    </div>

                    <div class="d-flex d-flex-row sm:d-flex-column align-items-center sm:items-end justify-content-between sm:justify-content-center g-3.5 pt-2 sm:pt-0 border-t border border-secondary sm:border-t-0">
                        <p class="fs-5 fw-bold text-secondary sm:text-end">Rp {{ number_format($groupSubtotal, 0, ',', '.') }}</p>
                        <div class="d-flex align-items-center g-3 sm:justify-content-end">
                            <button type="button" onclick="openEditEventModal('{{ $groupId }}')" class="px-2.5 py-1 rounded small fw-bold bg-light text-secondary hover:bg-light">
                                Edit
                            </button>
                            <button type="button" onclick="confirmDeleteEvent('{{ $groupId }}', {{ $customHeader?->id ?? $groupItems->first()->id }}, false)" class="px-2.5 py-1 rounded small fw-bold bg-danger text-white text-danger hover:bg-danger text-white">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Ringkasan Belanja & Tombol Lanjut Ke Pembayaran Global untuk Semua Event -->
            <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border-purple-100 d-flex d-flex-column sm:d-flex-row justify-content-between align-items-center g-3 sticky-top">
                <div>
                    <!-- <h4 class="fw-bold text-secondary fs-5">Ringkasan Belanja Acara</h4> -->
                    <div class="d-flex align-items-center g-3 mt-1 fs-6 text-secondary">
                        <!-- <span>Subtotal: <strong class="text-secondary">Rp {{ number_format($grupAcara->flatten()->sum('subtotal'), 0, ',', '.') }}</strong></span>
                        <span>•</span> -->
                        <span>Total: <strong class="text-secondary fw-bold fs-5">Rp {{ number_format($grupAcara->flatten()->sum('subtotal'), 0, ',', '.') }}</strong></span>
                    </div>
                </div>
                <a href="{{ route('pelanggan.acara.checkout.show', 'all') }}"
                    class="w-100 sm:w-auto text-center px-8 py-4 text-white fw-bold rounded hover:shadow text-base">Lanjut Ke Pembayaran</a>
            </div>
        @endif
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Daily Edit (tetap seperti lama) --}}
{{-- =============================== --}}
@php
    $allHarianCartsJson = $grupHarian->flatten()->keyBy('id')->map(function($c) {
        $basePrice = $c->menuHarian ? (float) $c->menuHarian->harga : ($c->opsiKustom ? (float) $c->opsiKustom->harga : 0);
        $name = $c->menuHarian->nama_menu ?? ($c->opsiKustom->name ?? 'Item');
        $serviceId = $c->menuHarian->layanan_katering_id ?? ($c->opsiKustom->layanan_katering_id ?? null);
        return [
            'id' => $c->id,
            'name' => $name,
            'harga' => $basePrice,
            'jumlah' => $c->jumlah,
            'service_id' => $serviceId,
            'produk_id' => $c->menuHarian_id,
            'extras' => $c->extras ?? [],
            'update_url' => route('pelanggan.keranjang.update', $c->id),
        ];
    });
@endphp

<div id="harianEditModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-lg mx-4 max-h-[90vh] d-flex d-flex-column">
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary d-flex-flex-shrink-0">
            <h3 class="fs-5 fw-bold text-secondary d-flex align-items-center">
                <svg style="width: 20px; height: 20px;" class="text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Ubah Pesanan</span>
            </h3>
            <button type="button" onclick="closeHarianEditModal()" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="overflow-y-auto d-flex-1 p-6">
            <form id="harianEditForm" action="" method="POST">
                @csrf @method('PUT')
                <div class="mb-6 bg-primary text-white/50 rounded p-4">
                    <h4 id="harianModalProductName" class="fw-bold text-secondary"></h4>
                    <p id="harianModalProductPrice" class="text-primary fw-bold fs-6"></p>
                </div>
                <div class="mb-6">
                    <label class="form-label fw-bold">Jumlah Porsi *</label>
                    <div class="d-flex align-items-center g-3">
                        <button type="button" onclick="changeHarianQty(-1)" style="height: 40px;" class="w-10 rounded bg-light hover:bg-light d-flex align-items-center justify-content-center text-secondary fw-bold fs-5">−</button>
                        <input type="text" inputmode="none" readonly tabindex="-1" name="jumlah" id="harianModalQty" value="1" class="form-control w-20 text-center px-3 py-2 rounded border border border-secondary fw-bold text-secondary focus: cursor-default select-none">
                        <button type="button" onclick="changeHarianQty(1)" style="height: 40px;" class="w-10 rounded bg-light hover:bg-light d-flex align-items-center justify-content-center text-secondary fw-bold fs-5">+</button>
                    </div>
                </div>
                <div id="harianModalExtrasLoading" class="fs-6 text-secondary py-2 d-none">Memuat opsi tambahan...</div>
                <div id="harianModalExtrasContainer" class="mb-5 d-none">
                    <label class="form-label fw-bold">Extra Tambahan (Opsional)</label>
                    <div id="harianModalExtrasList" class="divide-y divide-gray-100"></div>
                </div>
                <div class="rounded p-4 border border border-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-6 text-secondary">Estimasi Total</span>
                        <span id="harianModalTotal" class="fs-4 fw-bold text-primary">Rp 0</span>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-6 border-t border border-secondary d-flex-flex-shrink-0 d-flex g-3">
            <button type="button" onclick="closeHarianEditModal()" class="d-flex-1 px-6 py-3.5 bg-light text-secondary fw-bold rounded hover:bg-light fs-6">Batal</button>
            <button type="button" onclick="document.getElementById('harianEditForm').submit()" class="d-flex-1 px-6 py-3.5 text-white fw-bold rounded hover:shadow fs-6">Update</button>
        </div>
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Event Edit --}}
{{-- =============================== --}}
@php
    $grupAcaraJson = [];
    foreach($grupAcara as $gId => $gItems) {
        $pkgItem = $gItems->firstWhere('item_type', 'package');
        $customHeader = $gItems->firstWhere('item_type', 'custom_header');
        $setsQty = $customHeader ? (int) $customHeader->jumlah : ($pkgItem ? (int) $pkgItem->jumlah : 1);
        if ($setsQty <= 0) $setsQty = 1;

        $grupAcaraJson[$gId] = [
            'group_id' => $gId,
            'service_name' => $gItems->first()->layananKatering->name ?? 'Layanan Acara',
            'package_name' => $pkgItem?->paketKatering?->name,
            'package_quantity' => $pkgItem ? (int) $pkgItem->jumlah : 1,
            'package_portions_per_unit' => $pkgItem?->paketKatering?->total_portions ?? 0,
            'package_price' => (float) ($pkgItem?->paketKatering?->harga ?? 0),
            'sets_quantity' => $setsQty,
            'serving_name' => $gItems->first()->servingType?->name,
            'catatan' => $customHeader?->catatan ?? ($pkgItem?->catatan ?? ($gItems->first()->catatan ?? '')),
            'subtotal' => $gItems->sum(fn($c) => $c->subtotal),
            'layanan_katering_id' => $gItems->first()->layanan_katering_id,
            'catering_package_id' => $pkgItem?->catering_package_id,
            'serving_type_id' => $customHeader?->serving_type_id ?? ($pkgItem?->serving_type_id ?? ($gItems->first()->serving_type_id ?? null)),
            'is_package' => $pkgItem !== null,
            'min_portion' => $gItems->first()->layananKatering->min_portion ?? 1,
            'maksimal_porsi' => $gItems->first()->layananKatering->maksimal_porsi ?? 1000,
            'update_url' => route('pelanggan.acara.keranjang.update', $gId),
            'items' => $gItems->filter(fn($c) => in_array($c->item_type, ['package_item', 'custom_menu', 'addition']))->map(fn($c) => [
                'opsi_kustom_id' => $c->opsi_kustom_id,
                'jumlah' => $c->jumlah,
                'item_type' => $c->item_type,
                'name' => $c->opsiKustom->name ?? 'Item',
                'harga' => (float) ($c->opsiKustom->harga ?? 0),
                'subtotal' => (float) ($c->subtotal ?? 0),
            ])->values()->toArray(),
        ];
    }
@endphp

{{-- =============================== --}}
{{-- MODAL: Event Detail --}}
{{-- =============================== --}}
<div id="detailEventModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-lg mx-4 max-h-[90vh] d-flex d-flex-column">
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary d-flex-flex-shrink-0">
            <div>
                <span id="detailModalBadge" class="px-2.5 py-0.5 rounded-pill small fw-bold bg-purple-100 text-purple-700"></span>
                <h3 id="detailModalTitle" class="fs-5 fw-bold text-secondary mt-1"></h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="overflow-y-auto d-flex-1 p-6 d-flex flex-column gap-3">
            <div>
                <h5 class="small fw-bold uppercase tracking-wider text-secondary mb-2">Daftar Menu</h5>
                <div id="detailModalList" class="divide-y divide-gray-100 border border border-secondary rounded px-4 py-2"></div>
            </div>
            <div id="detailModalServingSection" class="d-none">
                <h5 class="small fw-bold uppercase tracking-wider text-secondary mb-1">Penyajian</h5>
                <p id="detailModalServing" class="fs-6 fw-medium text-secondary bg-light p-3 rounded border border border-secondary"></p>
            </div>
            <div id="detailModalNotesSection" class="d-none">
                <h5 class="small fw-bold uppercase tracking-wider text-secondary mb-1">Catatan</h5>
                <p id="detailModalNotes" class="fs-6 text-secondary italic bg-light p-3 rounded border border border-secondary"></p>
            </div>
        </div>
        <div class="p-6 border-t border border-secondary d-flex-flex-shrink-0 d-flex justify-content-between align-items-center bg-light rounded-b-2xl">
            <span class="fs-6 text-secondary">Total Harga Group</span>
            <span id="detailModalTotal" class="fs-5 fw-bold text-purple-600"></span>
        </div>
    </div>
</div>

<div id="editEventModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-2xl mx-4 max-h-[90vh] d-flex d-flex-column">
        <div class="d-flex align-items-center justify-content-between p-6 border-b border border-secondary d-flex-flex-shrink-0">
            <h3 class="fs-5 fw-bold text-secondary d-flex align-items-center">
                <svg style="width: 20px; height: 20px;" class="text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit Pesanan Acara</span>
            </h3>
            <button type="button" onclick="closeEditEventModal()" class="p-1 text-secondary hover:text-secondary rounded hover:bg-light">
                <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="overflow-y-auto d-flex-1 p-6">
            <style>
            @media (min-width: 1024px) {
                /* Chrome, Safari, Edge, Opera */
                input[type=number].desktop-no-spinner::-webkit-outer-spin-button,
                input[type=number].desktop-no-spinner::-webkit-inner-spin-button,
                input[type=number].edit-acara-qty::-webkit-outer-spin-button,
                input[type=number].edit-acara-qty::-webkit-inner-spin-button {
                    -webkit-appearance: none;
                    margin: 0;
                }
                /* Firefox */
                input[type=number].desktop-no-spinner,
                input[type=number].edit-acara-qty {
                    -moz-appearance: textfield;
                }
            }
            </style>
            <form id="editAcaraForm" action="" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="layanan_katering_id" id="editEventServiceId">
                <input type="hidden" name="catering_package_id" id="editEventPackageId">

                {{-- Info Header --}}
                <div id="editEventHeader" class="bg-light rounded p-4 mb-6">
                    <p class="fw-bold text-secondary" id="editEventTitle"></p>
                    <p class="fs-6 text-purple-600" id="editAcaraType"></p>
                </div>

                {{-- Menu Items (akan di-generate JS) --}}
                <div class="mb-6">
                    <h4 class="fw-bold text-secondary mb-3" id="editEventMenuHeading">Menu</h4>
                    <div id="editEventMenuList" class="divide-y divide-gray-100 border-t border-b border border-secondary"></div>

                    {{-- Porsi Indicator (hanya untuk custom menu) --}}
                    <div id="editCustomPortionIndicator" class="mt-4 p-4 rounded border border border-secondary bg-light d-flex align-items-center justify-content-between" style="display:none;">
                        <span class="text-base fw-bold text-secondary">Total Porsi : <span id="editCustomPortionsDisplay">0</span> / <span id="editCustomMaxTarget">100</span></span>
                    </div>
                </div>

                {{-- Extra --}}
                <div id="editEventExtrasSection" class="mb-6" style="display:none;">
                    <h4 class="fw-bold text-secondary mb-3">Extra</h4>
                    <div id="editEventExtrasList" class="divide-y divide-gray-100 border-t border-b border border-secondary"></div>
                </div>

                {{-- Penyajian --}}
                <div id="editEventServingSection" class="mb-6" style="display:none;">
                    <h4 class="fw-bold text-secondary mb-3">Penyajian</h4>
                    <div id="editEventServingList" class="d-flex d-flex-column sm:d-flex-row sm:d-flex-wrap g-3 sm:g-3 pt-1"></div>
                </div>

                {{-- Total --}}
                <div class="rounded p-4 border border border-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-6 text-secondary">Total Harga</span>
                        <span id="editEventTotal" class="fs-4 fw-bold text-primary">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="fs-6 text-secondary">Total Porsi</span>
                        <span class="fw-bold text-secondary">
                            <span id="editEventPortions">0</span> Porsi
                        </span>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-6 border-t border border-secondary d-flex-flex-shrink-0 d-flex g-3">
            <button type="button" onclick="closeEditEventModal()" class="d-flex-1 px-6 py-3.5 bg-light text-secondary fw-bold rounded hover:bg-light fs-6">Batal</button>
            <button type="button" id="editEventSubmitBtn" onclick="submitEditEventForm()" class="d-flex-1 px-6 py-3.5 text-white fw-bold rounded hover:shadow fs-6">Update</button>
        </div>
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Delete Confirmation --}}
{{-- =============================== --}}
<div id="deleteConfirmModal" class="position-fixed d-flex align-items-center justify-content-center bg-dark/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-100 max-w-sm mx-4 p-6 text-center">
        <div style="width: 64px; height: 64px;" class="mx-auto mb-4 text-danger d-flex align-items-center justify-content-center">
            <svg style="width: 48px; height: 48px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 class="fs-5 fw-bold text-secondary mb-2">Hapus Pesanan?</h3>
        <p class="text-secondary mb-6">Apakah Anda ingin menghapus pesanan ini?</p>
        <div class="d-flex g-3">
            <button type="button" onclick="closeDeleteModal()" class="d-flex-1 px-6 py-3 bg-light text-secondary fw-bold rounded hover:bg-light">Tidak</button>
            <form id="deleteEventForm" action="" method="POST" class="d-flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // =======================
    // TAB SWITCHING
    // =======================
    function switchTab(tab) {
        document.getElementById('content-harian').style.display = tab === 'harian' ? '' : 'none';
        document.getElementById('content-acara').style.display = tab === 'acara' ? '' : 'none';

        const tabDaily = document.getElementById('tab-harian');
        const tabEvent = document.getElementById('tab-acara');
        tabDaily.classList.toggle('border-orange-500', tab === 'harian');
        tabDaily.classList.toggle('text-orange-600', tab === 'harian');
        tabDaily.classList.toggle('border-transparent', tab !== 'harian');
        tabDaily.classList.toggle('text-gray-500', tab !== 'harian');
        tabEvent.classList.toggle('border-orange-500', tab === 'acara');
        tabEvent.classList.toggle('text-orange-600', tab === 'acara');
        tabEvent.classList.toggle('border-transparent', tab !== 'acara');
        tabEvent.classList.toggle('text-gray-500', tab !== 'acara');
    }

    // =======================
    // DAILY EDIT MODAL
    // =======================
    const harianCartsData = @json($allHarianCartsJson);
    let currentHarianCart = null;
    let harianAvailableExtras = [];

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    function openHarianEditModal(cartId) {
        currentHarianCart = harianCartsData[cartId];
        if (!currentHarianCart) return;

        document.getElementById('harianEditForm').action = currentHarianCart.update_url;
        document.getElementById('harianModalProductName').textContent = currentHarianCart.name;
        document.getElementById('harianModalProductPrice').textContent = formatRupiah(currentHarianCart.harga);
        document.getElementById('harianModalQty').value = currentHarianCart.jumlah;

        document.getElementById('harianModalExtrasList').innerHTML = '';
        document.getElementById('harianModalExtrasContainer').classList.add('hidden');
        document.getElementById('harianModalExtrasLoading').classList.remove('hidden');

        let extrasUrl = `/api/service/${currentHarianCart.service_id}/custom-options`;
        if (false) {} else {
            if (qtyContainer) {
                qtyContainer.classList.add('hidden');
                qtyContainer.classList.remove('flex');
            }
            if (qtyInput) {
                qtyInput.value = 0;
                qtyInput.disabled = true;
            }
        }
        updateDailyModalTotal();
    }

    function changeDailyExtraQty(id, delta) {
        const cb = document.getElementById('harian_extra_cb_' + id);
        if (!cb || !cb.checked) return;
        const input = document.getElementById('daily_extra_qty_' + id);
        let val = parseInt(input.value) || 0;
        val += delta;
        if (val < 1) { cb.checked = false; toggleHarianExtra(id); } else { input.value = val; updateDailyModalTotal(); }
    }

    function manualDailyExtraQty(id) {
        const cb = document.getElementById('harian_extra_cb_' + id);
        if (!cb || !cb.checked) return;
        const input = document.getElementById('daily_extra_qty_' + id);
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) { if (input.value === "") return; val = 1; input.value = 1; }
        updateDailyModalTotal();
    }

    function updateDailyModalTotal() {
        if (!currentHarianCart) return;
        const qty = parseInt(document.getElementById('harianModalQty').value) || 1;
        let total = currentHarianCart.harga * qty;
        document.querySelectorAll('.harian-extra-checkbox:checked').forEach(cb => {
            const extraId = cb.value;
            const extraQty = parseInt(document.getElementById('daily_extra_qty_' + extraId).value) || 0;
            total += parseFloat(cb.getAttribute('data-harga')) * extraQty;
        });
        document.getElementById('harianModalTotal').textContent = formatRupiah(total);
    }

    document.getElementById('harianEditModal').addEventListener('click', function(e) { if (e.target === this) closeHarianEditModal(); });

    // =======================
    // EVENT EDIT MODAL
    // =======================
    const eventGroupsData = @json($grupAcaraJson);
    let editingGroupId = null;
    let editServiceOptions = [];
    let initialEditEventSnapshot = null;

    function openEditEventModal(groupId) {
        editingGroupId = groupId;
        initialEditEventSnapshot = null;
        const group = eventGroupsData[groupId];
        if (!group || group.is_package) return;

        document.getElementById('editAcaraForm').action = group.update_url;
        document.getElementById('editEventServiceId').value = group.layanan_katering_id;
        document.getElementById('editEventPackageId').value = group.catering_package_id || '';

        fetch(`/api/service/${group.layanan_katering_id}/custom-options`)
            .then(res => res.json())
            .then(options => {
                editServiceOptions = options;
                renderEditEventModal(group, options);
            });

        document.getElementById('editEventModal').style.display = 'flex';
    }

    function renderEditEventModal(group, options) {
        if (group.is_package) return;
        const menus = options.filter(o => o.type === 'menu');
        const extras = options.filter(o => o.type === 'extra');
        const servings = options.filter(o => o.type === 'tipe_penyajian');

        document.getElementById('editEventTitle').textContent = 'Custom Menu';
        document.getElementById('editAcaraType').textContent = group.service_name || 'Layanan Acara';
        let menuHtml = '';
        menus.forEach((menu, idx) => {
            const existingItem = group.items.find(i => i.opsi_kustom_id == menu.id && i.item_type === 'custom_menu');
            const qty = existingItem ? existingItem.jumlah : 0;
            const checked = qty > 0 ? 'checked' : '';
            const opacityClass = qty > 0 ? '' : 'opacity-0 pointer-events-none';

            let detailBtn = '';
            let detailDiv = '';
            if (menu.items && Array.isArray(menu.items) && menu.items.length > 0) {
                detailBtn = `<button type="button" onclick="toggleEditMenuAcaraDetail(${idx}, event)" class="small fw-bold text-info hover:text-info focus: underline">Lihat Detail</button>`;
                let itemsListHtml = menu.items.map(item => `<li>${item}</li>`).join('');
                detailDiv = `<div id="edit_menu_detail_${idx}" class="d-none ps-7 mt-2"><div class="py-2.5 px-3.5 bg-light/80 small text-secondary border-l-2 border border-primary rounded-r-lg"><ul class="list-disc list-inside space-y-1">${itemsListHtml}</ul></div></div>`;
            }

            menuHtml += `
            <div class="py-3.5 px-2 hover:bg-primary text-white/50 rounded">
                <div class="d-flex align-items-center justify-content-between g-3">
                    <div class="d-flex align-items-center g-3 d-flex-1 min-w-0">
                        <input type="checkbox" id="edit_menu_cb_${idx}" data-idx="${idx}" data-id="${menu.id}" data-harga="${menu.harga}"
                            style="width: 16px; height: 16px;" class="rounded border border-secondary text-primary edit-menu-checkbox flex-shrink-0 cursor-pointer" ${checked} onchange="toggleEditMenu(${idx}, ${menu.id})">
                        <label for="edit_menu_cb_${idx}" class="fs-6 sm:text-base fw-bold text-secondary cursor-pointer truncate">${menu.name}</label>
                    </div>
                    <div class="d-flex align-items-center g-3.5 transition-opacity flex-shrink-0 ${opacityClass}" id="edit_menu_qty_container_${idx}">
                        <button type="button" onclick="changeEditEventQty(${idx}, -1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0">−</button>
                        <input type="number" id="edit_event_qty_${idx}" value="${qty}" min="1"
                            class="form-control w-14 text-center py-1 border border border-secondary rounded small sm:fs-6 fw-bold text-secondary bg-white flex-shrink-0 focus: focus:border border-primary -1 desktop-no-spinner edit-acara-qty" data-idx="${idx}" data-id="${menu.id}" data-harga="${menu.harga}" data-type="custom_menu" oninput="validateEditAcaraInput(${idx})" onchange="validateEditAcaraInputBlur(${idx})">
                        <button type="button" onclick="changeEditEventQty(${idx}, 1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0 edit-menu-plus-btn">+</button>
                    </div>
                </div>
                <div class="ps-7 mt-1">
                    <span class="small sm:fs-6 fw-bold text-primary">Rp ${Number(menu.harga).toLocaleString('id-ID')} / porsi</span>
                </div>
                ${detailBtn ? `<div class="ps-7 mt-1.5">${detailBtn}</div>` : ''}
                ${detailDiv}
            </div>`;
        });
        document.getElementById('editEventMenuList').innerHTML = menuHtml;

        let extraStartIdx = menus.length;
        if (extras.length > 0) {
            document.getElementById('editEventExtrasSection').style.display = 'block';
            let extraHtml = '';
            extras.forEach((extra, i) => {
                const eIdx = extraStartIdx + i;
                const existingExtra = group.items.find(item => item.opsi_kustom_id == extra.id && item.item_type === 'addition');
                const eQty = existingExtra ? existingExtra.jumlah : 0;
                const eChecked = eQty > 0 ? 'checked' : '';

                extraHtml += `
                <div class="py-3 px-2 d-flex align-items-center justify-content-between g-3 hover:bg-primary text-white/50 rounded">
                    <label for="edit_extra_cb_${eIdx}" class="d-flex align-items-center g-3 cursor-pointer d-flex-1 min-w-0">
                        <input type="checkbox" id="edit_extra_cb_${eIdx}" data-idx="${eIdx}" data-id="${extra.id}" data-harga="${extra.harga}"
                            style="width: 16px; height: 16px;" class="rounded border border-secondary text-primary edit-extra-checkbox flex-shrink-0 cursor-pointer" ${eChecked} onchange="toggleEditExtra(${eIdx}, ${extra.id})">
                        <span class="fs-6 fw-medium text-secondary truncate">${extra.name}</span>
                    </label>
                    <span class="fs-6 fw-bold text-primary flex-shrink-0">+Rp ${Number(extra.harga).toLocaleString('id-ID')}</span>
                    <input type="hidden" id="edit_event_qty_${eIdx}" value="${eQty}"
                        class="edit-acara-qty" data-idx="${eIdx}" data-id="${extra.id}" data-harga="${extra.harga}" data-type="addition">
                </div>`;
            });
            document.getElementById('editEventExtrasList').innerHTML = extraHtml;
        } else {
            document.getElementById('editEventExtrasSection').style.display = 'none';
        }

        if (servings.length > 0) {
            document.getElementById('editEventServingSection').style.display = 'block';
            let servingHtml = '';
            servings.forEach(s => {
                const checked = group.serving_type_id == s.id ? 'checked' : '';
                servingHtml += `
                <label class="d-flex align-items-center g-3 py-2 px-3 rounded hover:bg-primary text-white/50 cursor-pointer border border border-secondary sm:border-transparent sm:hover:border border-secondary">
                    <input type="radio" name="serving_type_id" value="${s.id}" style="width: 16px; height: 16px;" class="text-primary border border-secondary cursor-pointer flex-shrink-0" ${checked} onchange="recalcEditEvent()">
                    <span class="fs-6 fw-medium text-secondary">${s.name}</span>
                </label>`;
            });
            document.getElementById('editEventServingList').innerHTML = servingHtml;
        } else {
            document.getElementById('editEventServingSection').style.display = 'none';
        }

        const heading = document.getElementById('editEventMenuHeading');
        if (heading) heading.style.display = 'block';
        document.getElementById('editCustomPortionIndicator').style.display = 'flex';
        initialEditEventSnapshot = getEditEventSnapshot();
        recalcEditEvent();
    }

    function toggleEditMenuAcaraDetail(idx, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const detailPanel = document.getElementById('edit_menu_detail_' + idx);
        if (detailPanel) {
            detailPanel.classList.toggle('hidden');
        }
    }

    function toggleEditMenu(idx, menuId) {
        const cb = document.getElementById('edit_menu_cb_' + idx);
        const container = document.getElementById('edit_menu_qty_container_' + idx);
        const input = document.getElementById('edit_event_qty_' + idx);

        if (cb.checked) {
            if (container) container.classList.remove('opacity-0', 'pointer-events-none');
            if (input && (parseInt(input.value) || 0) <= 0) input.value = 1;
        } else {
            if (container) container.classList.add('opacity-0', 'pointer-events-none');
            if (input) input.value = 0;
        }
        recalcEditEvent();
    }

    function toggleEditExtra(idx, extraId) {
        const cb = document.getElementById('edit_extra_cb_' + idx);
        const input = document.getElementById('edit_event_qty_' + idx);

        if (!cb.checked && input) {
            input.value = 0;
        }
        recalcEditEvent();
    }

    function changeEditEventQty(idx, delta) {
        const input = document.getElementById('edit_event_qty_' + idx);
        const cb = document.getElementById('edit_menu_cb_' + idx);
        if (!input) return;
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        if (val <= 0 && cb) {
            cb.checked = false;
            const container = document.getElementById('edit_menu_qty_container_' + idx);
            if (container) container.classList.add('opacity-0', 'pointer-events-none');
        } else if (cb && !cb.checked && val > 0) {
            cb.checked = true;
            const container = document.getElementById('edit_menu_qty_container_' + idx);
            if (container) container.classList.remove('opacity-0', 'pointer-events-none');
        }
        input.value = val;
        recalcEditEvent();
    }

    function getEditEventSnapshot() {
        if (!editingGroupId) return '';
        const group = eventGroupsData[editingGroupId];
        if (!group || group.is_package) return '';

        const items = [];
        document.querySelectorAll('#editAcaraForm input.edit-acara-qty').forEach(input => {
            const idx = input.dataset.idx || input.id.split('_').pop();

            let active = true;
            const menuCb = document.getElementById('edit_menu_cb_' + idx);
            const extraCb = document.getElementById('edit_extra_cb_' + idx);
            if (menuCb && !menuCb.checked) active = false;
            if (extraCb && !extraCb.checked) active = false;

            if (active) {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    const optId = input.dataset.id || (menuCb ? menuCb.dataset.id : (extraCb ? extraCb.dataset.id : ''));
                    if (optId) {
                        items.push(optId + ':' + qty);
                    }
                }
            }
        });
        items.sort();

        const servingRadio = document.querySelector('#editAcaraForm input[name="serving_type_id"]:checked');
        const servingVal = servingRadio ? servingRadio.value : '';

        return 'custom:' + items.join('|') + '||' + servingVal;
    }

    function validateEditAcaraInput(idx) {
        const inputEl = document.getElementById('edit_event_qty_' + idx);
        if (!inputEl) return;
        inputEl.value = inputEl.value.replace(/[^0-9]/g, '');
        if (inputEl.value !== '') {
            let val = parseInt(inputEl.value, 10);
            if (isNaN(val) || val < 1) {
                val = 1;
                inputEl.value = '1';
            }
        }
        recalcEditEvent();
    }

    function validateEditAcaraInputBlur(idx) {
        const inputEl = document.getElementById('edit_event_qty_' + idx);
        if (!inputEl) return;
        inputEl.value = inputEl.value.replace(/[^0-9]/g, '');
        let val = parseInt(inputEl.value, 10);
        if (isNaN(val) || val < 1) {
            inputEl.value = '1';
        }
        recalcEditEvent();
    }

    function recalcEditEvent() {
        const group = eventGroupsData[editingGroupId];
        if (!group) return;

        const btn = document.getElementById('editEventSubmitBtn');
        let isValid = true;
        if (group.is_package) return;

        let totalPrice = 0;
        let totalPortions = 0;
        const maxPortion = group.maksimal_porsi || 1000;

            // 1. Hitung totalPorsi dan harga menu terlebih dahulu dari custom_menu yang terpilih
            document.querySelectorAll('.edit-acara-qty').forEach(input => {
                const type = input.dataset.type;
                if (type !== 'custom_menu') return;

                const idx = input.dataset.idx || input.id.split('_').pop();
                const menuCb = document.getElementById('edit_menu_cb_' + idx);
                if (menuCb && !menuCb.checked) return;

                let qty = parseInt(input.value) || 0;
                const harga = parseFloat(input.dataset.harga) || 0;
                if (qty < 1 && input.value !== '') {
                    qty = 1;
                    input.value = '1';
                }

                if (totalPortions + qty > maxPortion) {
                    qty = Math.max(1, maxPortion - totalPortions);
                    input.value = qty;
                }
                totalPortions += qty;
                totalPrice += harga * qty;
            });

            // 2. Hitung harga extra mengikuti Total Porsi menu hasil penjumlahan
            document.querySelectorAll('.edit-acara-qty').forEach(input => {
                const type = input.dataset.type;
                if (type !== 'addition') return;

                const idx = input.dataset.idx || input.id.split('_').pop();
                const extraCb = document.getElementById('edit_extra_cb_' + idx);
                if (extraCb && !extraCb.checked) return;

                const harga = parseFloat(input.dataset.harga) || 0;
                input.value = totalPortions;
                totalPrice += harga * totalPortions;
            });

            document.getElementById('editEventTotal').textContent = formatRupiah(totalPrice);
            document.getElementById('editEventPortions').textContent = totalPortions;

            const displayPortions = document.getElementById('editCustomPortionsDisplay');
            const displayMax = document.getElementById('editCustomMaxTarget');

            if (displayPortions) displayPortions.textContent = totalPortions;
            if (displayMax) displayMax.textContent = maxPortion;

            const isMaxReached = totalPortions >= maxPortion;
            document.querySelectorAll('#editEventMenuList .edit-menu-plus-btn').forEach(plusBtn => {
                plusBtn.disabled = isMaxReached;
                plusBtn.classList.toggle('opacity-50', isMaxReached);
                plusBtn.classList.toggle('cursor-not-allowed', isMaxReached);
            });

            const hasServing = document.querySelector('#editAcaraForm input[name="serving_type_id"]:checked');
            const isServingRequired = document.querySelector('#editAcaraForm input[name="serving_type_id"]') !== null;

            isValid = totalPortions > 0 && totalPortions <= maxPortion && (!isServingRequired || hasServing);

            if (!isValid) {
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else {
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
            buildEditEventPayload(totalPortions);
    }

    function buildEditEventPayload(totalPortions = 0) {
        document.querySelectorAll('.edit-acara-payload-item').forEach(el => el.remove());

        const form = document.getElementById('editAcaraForm');
        if (!form) return;
        let formIdx = 0;

        document.querySelectorAll('.edit-menu-checkbox:checked').forEach(cb => {
            const id = cb.dataset.id;
            const idx = cb.dataset.idx || cb.id.split('_').pop();
            const inputEl = document.getElementById('edit_event_qty_' + idx);
            const qty = parseInt(inputEl ? inputEl.value : 0) || 0;
            if (qty > 0 && id) {
                appendEditEventHidden(form, `items[${formIdx}][opsi_kustom_id]`, id);
                appendEditEventHidden(form, `items[${formIdx}][jumlah]`, qty);
                appendEditEventHidden(form, `items[${formIdx}][item_type]`, 'custom_menu');
                formIdx++;
            }
        });

        document.querySelectorAll('.edit-extra-checkbox:checked').forEach(cb => {
            const id = cb.dataset.id;
            if (totalPortions > 0 && id) {
                appendEditEventHidden(form, `items[${formIdx}][opsi_kustom_id]`, id);
                appendEditEventHidden(form, `items[${formIdx}][jumlah]`, totalPortions);
                appendEditEventHidden(form, `items[${formIdx}][item_type]`, 'addition');
                formIdx++;
            }
        });
    }

    function appendEditEventHidden(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        input.className = 'edit-acara-payload-item';
        form.appendChild(input);
    }

    async function submitEditEventForm() {
        const btn = document.getElementById('editEventSubmitBtn');
        if (btn.disabled) return;

        const group = eventGroupsData[editingGroupId];
        if (!group) return;

        const currentSnapshot = getEditEventSnapshot();
        if (initialEditEventSnapshot !== null && currentSnapshot === initialEditEventSnapshot) {
            closeEditEventModal();
            return;
        }

        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        const origText = btn.textContent;
        btn.textContent = 'Update';

        buildEditEventPayload(parseInt(document.getElementById('editEventPortions')?.textContent) || 0);
        const form = document.getElementById('editAcaraForm');
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (response.ok && data.success) {
                closeEditEventModal();
                await refreshCartDOM();
                Swal.fire({
                    title: 'Berhasil',
                    text: group.is_package ? 'Paket berhasil diperbarui.' : 'Custom menu berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#f97316'
                });
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.textContent = origText;
                Swal.fire({
                    title: 'Gagal',
                    text: data.message || 'Gagal memperbarui pesanan.',
                    icon: 'error',
                    confirmButtonColor: '#f97316'
                });
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.textContent = origText;
            Swal.fire({
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menyimpan perubahan.',
                icon: 'error',
                confirmButtonColor: '#f97316'
            });
        }
    }

    function closeEditEventModal() {
        document.getElementById('editEventModal').style.display = 'none';
    }

    document.getElementById('editEventModal').addEventListener('click', function(e) { if (e.target === this) closeEditEventModal(); });

    // =======================
    // DETAIL EVENT MODAL
    // =======================
    function openDetailModal(groupId) {
        const group = eventGroupsData[groupId];
        if (!group) return;
        
        document.getElementById('detailModalBadge').textContent = group.is_package ? 'Paket' : 'Custom Menu';
        document.getElementById('detailModalBadge').className = `px-2.5 py-0.5 rounded-full text-xs font-bold ${group.is_package ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'}`;
        document.getElementById('detailModalTitle').textContent = group.is_package && group.package_name ? `${group.service_name} — ${group.package_name}` : group.service_name;
        
        let html = '';
        group.items.forEach(i => {
            const badge = i.item_type === 'package_item' ? '<span class="small text-success fw-medium">(Termasuk)</span>' : '';
            const priceStr = i.item_type === 'package_item' ? '' : `Rp ${Number(i.subtotal).toLocaleString('id-ID')}`;
            html += `
            <div class="py-2.5 d-flex align-items-center justify-content-between fs-6">
                <div>
                    <span class="fw-medium text-secondary">${i.name}</span>
                    <span class="text-secondary small ms-1">× ${i.jumlah}</span>
                    ${badge}
                </div>
                <span class="fw-medium text-secondary">${priceStr}</span>
            </div>`;
        });
        document.getElementById('detailModalList').innerHTML = html || '<p class="fs-6 text-secondary py-2">Tidak ada menu</p>';
        
        if (group.serving_name) {
            document.getElementById('detailModalServingSection').classList.remove('hidden');
            document.getElementById('detailModalServing').textContent = group.serving_name;
        } else {
            document.getElementById('detailModalServingSection').classList.add('hidden');
        }
        
        if (group.catatan) {
            document.getElementById('detailModalNotesSection').classList.remove('hidden');
            document.getElementById('detailModalNotes').textContent = group.catatan;
        } else {
            document.getElementById('detailModalNotesSection').classList.add('hidden');
        }
        
        document.getElementById('detailModalTotal').textContent = `Rp ${Number(group.subtotal).toLocaleString('id-ID')}`;
        document.getElementById('detailEventModal').style.display = 'flex';
    }

    function closeDetailModal() {
        document.getElementById('detailEventModal').style.display = 'none';
    }

    document.getElementById('detailEventModal')?.addEventListener('click', function(e) { if (e.target === this) closeDetailModal(); });

    // =======================
    // DELETE CONFIRMATION (SweetAlert)
    // =======================
    function confirmDeleteEvent(groupId, cartId, isPackage = false) {
        Swal.fire({
            title: 'Hapus item?',
            text: 'Item yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/dashboard/keranjang/${cartId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(async (data) => {
                    if (data.success) {
                        await refreshCartDOM();
                        if (typeof window.updateCartBadges === 'function' && typeof data.cart_count !== 'undefined') {
                            window.updateCartBadges(data.cart_count);
                        }
                        Swal.fire({
                            title: 'Berhasil',
                            text: isPackage ? 'Paket berhasil dihapus.' : 'Custom menu berhasil dihapus.',
                            icon: 'success',
                            confirmButtonColor: '#f97316'
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.message || 'Gagal menghapus item.',
                            icon: 'error',
                            confirmButtonColor: '#f97316'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menghapus item.',
                        icon: 'error',
                        confirmButtonColor: '#f97316'
                    });
                });
            }
        });
    }

    async function refreshCartDOM() {
        try {
            const response = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newDaily = doc.getElementById('content-harian');
            const newEvent = doc.getElementById('content-acara');
            const newTabDaily = doc.getElementById('tab-harian');
            const newTabEvent = doc.getElementById('tab-acara');

            if (newDaily && document.getElementById('content-harian')) {
                document.getElementById('content-harian').innerHTML = newDaily.innerHTML;
            }
            if (newEvent && document.getElementById('content-acara')) {
                document.getElementById('content-acara').innerHTML = newEvent.innerHTML;
            }
            if (newTabDaily && document.getElementById('tab-harian')) {
                document.getElementById('tab-harian').innerHTML = newTabDaily.innerHTML;
            }
            if (newTabEvent && document.getElementById('tab-acara')) {
                document.getElementById('tab-acara').innerHTML = newTabEvent.innerHTML;
            }
            if (typeof window.refreshCartBadges === 'function') {
                window.refreshCartBadges();
            }

            doc.querySelectorAll('script').forEach(s => {
                const text = s.textContent || '';
                if (text.includes('const eventGroupsData =')) {
                    const matchEvent = text.match(/const eventGroupsData = (\{[\s\S]*?\});\s*(?:let|const|function|var|\n|$)/);
                    if (matchEvent && matchEvent[1]) {
                        try {
                            const parsed = JSON.parse(matchEvent[1]);
                            for (let k in eventGroupsData) delete eventGroupsData[k];
                            Object.assign(eventGroupsData, parsed);
                        } catch(e) {}
                    }
                }
                if (text.includes('const harianCartsData =')) {
                    const matchDaily = text.match(/const harianCartsData = (\{[\s\S]*?\});\s*(?:let|const|function|var|\n|$)/);
                    if (matchDaily && matchDaily[1]) {
                        try {
                            const parsed = JSON.parse(matchDaily[1]);
                            for (let k in harianCartsData) delete harianCartsData[k];
                            Object.assign(harianCartsData, parsed);
                        } catch(e) {}
                    }
                }
            });
        } catch(e) {
            console.error('refreshCartDOM error:', e);
            window.location.reload();
        }
    }
</script>
@endpush
@push('styles')
<style>
@media (min-width: 1024px) {
    /* Chrome, Safari, Edge, Opera */
    input[type=number].desktop-no-spinner::-webkit-outer-spin-button,
    input[type=number].desktop-no-spinner::-webkit-inner-spin-button,
    input[type=number].edit-acara-qty::-webkit-outer-spin-button,
    input[type=number].edit-acara-qty::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    /* Firefox */
    input[type=number].desktop-no-spinner,
    input[type=number].edit-acara-qty {
        -moz-appearance: textfield;
    }
}
</style>
@endpush
@endsection
