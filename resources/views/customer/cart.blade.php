@extends('layouts.app')
@section('title', 'Keranjang Belanja')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <svg class="w-7 h-7 text-orange-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
        <span>Keranjang <span class="text-orange-500">Belanja</span></span>
    </h2>

    @php
        $packageGroupsCount = $eventGroups->filter(fn($items) => $items->firstWhere('item_type', 'package') !== null)->count();
        $customGroupsCount = $eventGroups->filter(fn($items) => $items->firstWhere('item_type', 'package') === null)->count();
        $totalEventBadge = $packageGroupsCount + $customGroupsCount;
    @endphp

    {{-- Tab Navigation --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button type="button" onclick="switchTab('daily')" id="tab-daily"
            class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center {{ $activeTab === 'daily' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Daily</span>
            @if($dailyGroups->isNotEmpty())
                <span class="ml-1 px-2 py-0.5 bg-orange-100 text-orange-600 rounded-full text-xs font-bold">{{ $dailyGroups->flatten()->count() }}</span>
            @endif
        </button>
        <button type="button" onclick="switchTab('event')" id="tab-event"
            class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center {{ $activeTab === 'event' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            <span>Event</span>
            @if($totalEventBadge > 0)
                <span class="ml-1 px-2 py-0.5 bg-purple-100 text-purple-600 rounded-full text-xs font-bold">{{ $totalEventBadge }}</span>
            @endif
        </button>
    </div>

    {{-- =============================== --}}
    {{-- TAB DAILY --}}
    {{-- =============================== --}}
    <div id="content-daily" style="{{ $activeTab !== 'daily' ? 'display:none' : '' }}">
        @if($dailyGroups->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
                <div class="w-16 h-16 mx-auto mb-4 text-gray-300 flex items-center justify-center">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
                <p class="text-gray-500 font-medium mb-4">Keranjang harian Anda kosong</p>
                <a href="{{ route('customer.products') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-medium rounded-full hover:bg-orange-600 transition-colors">
                    <span>Lihat Menu</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 space-y-6">
                @foreach($dailyGroups->flatten() as $cart)
                @php
                    $basePrice = $cart->product ? (float) $cart->product->price : ($cart->customOption ? (float) $cart->customOption->price : 0);
                    $extrasList = collect();
                    $extrasPrice = 0;
                    if (!empty($cart->extras)) {
                        $extraIds = array_column($cart->extras, 'id');
                        $options = \App\Models\CustomOption::whereIn('id', $extraIds)->get()->keyBy('id');
                        foreach ($cart->extras as $extraData) {
                            if ($opt = $options->get($extraData['id'])) {
                                $exPrice = (float) $opt->price * $extraData['qty'];
                                $extrasPrice += $exPrice;
                                $extrasList->push((object)[
                                    'name' => $opt->name,
                                    'qty' => $extraData['qty'],
                                    'price' => $exPrice,
                                ]);
                            }
                        }
                    }
                @endphp
                <div class="flex items-start space-x-4 {{ !$loop->last ? 'border-b border-gray-100 pb-6' : '' }}">
                    <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">
                        @if($cart->product && $cart->product->image)
                            <img src="{{ Storage::url($cart->product->image) }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        @endif
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex sm:items-start justify-between flex-col sm:flex-row gap-2">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $cart->product->name ?? ($cart->customOption->name ?? 'Item') }}</h4>
                                <p class="text-sm font-medium text-gray-700">{{ $cart->quantity }} Porsi</p>
                            </div>
                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</p>
                        </div>
                        
                        @if($extrasList->isNotEmpty())
                            <div class="text-sm text-gray-600 mt-2">
                                <span class="font-medium text-gray-800">Extra:</span> 
                                {{ collect($extrasList)->map(fn($ex) => $ex->name . ' ×' . $ex->qty)->implode(', ') }}
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4 space-x-3">
                            <button type="button" onclick="openDailyEditModal({{ $cart->id }})" class="px-4 py-2 bg-orange-100 text-orange-700 text-sm font-semibold rounded-lg hover:bg-orange-200 transition-colors">Ubah Pesanan</button>
                            <form action="{{ route('customer.cart.destroy', $cart) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-100 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tombol Checkout Global untuk Semua Daily -->
            <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border-orange-100 flex flex-col sm:flex-row justify-between items-center gap-4 sticky bottom-4 z-10">
                <div>
                    <h4 class="font-bold text-gray-900 text-lg">Total Seluruh Pesanan Daily</h4>
                    <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($dailyGroups->flatten()->sum('subtotal'), 0, ',', '.') }}</p>
                    <!-- <p class="text-sm text-gray-500 mt-1">Satu kali checkout untuk seluruh menu harian.</p> -->
                </div>
                <a href="{{ route('customer.checkout') }}"
                    class="w-full sm:w-auto text-center px-8 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold rounded-xl hover:shadow-lg transition-all text-lg flex items-center justify-center gap-2">
                    Checkout 
                </a>
            </div>
        @endif
    </div>

    {{-- =============================== --}}
    {{-- TAB EVENT --}}
    {{-- =============================== --}}
    <div id="content-event" style="{{ $activeTab !== 'event' ? 'display:none' : '' }}">
        @if($eventGroups->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
                <div class="w-16 h-16 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium mb-4">Belum ada pesanan event</p>
                <a href="{{ route('landing') }}#services" class="inline-flex items-center px-6 py-3 bg-purple-500 text-white font-medium rounded-full hover:bg-purple-600 transition-colors">
                    <span>Pilih Layanan Event</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        @else
            @php
                $packageGroups = $eventGroups->filter(fn($items) => $items->firstWhere('item_type', 'package') !== null);
                $customGroups = $eventGroups->filter(fn($items) => $items->firstWhere('item_type', 'package') === null);
            @endphp

            <div class="divide-y divide-gray-200 border-t border-b border-gray-200 my-2">
                {{-- Kelompok 1: Paket Event --}}
                @foreach($packageGroups as $groupId => $groupItems)
                @php
                    $packageItem = $groupItems->firstWhere('item_type', 'package');
                    $service = $groupItems->first()->cateringService;
                    $groupSubtotal = $groupItems->sum(fn($c) => $c->subtotal);
                @endphp
                <div class="py-6 first:pt-4 last:pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4" id="event-card-{{ $groupId }}">
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="font-bold text-gray-900 text-base sm:text-lg">{{ $packageItem->cateringPackage->name ?? 'Paket' }} ({{ $packageItem->quantity }})</span>
                            <span class="text-gray-400 font-medium">·</span>
                            <span class="text-sm text-gray-500 font-medium">{{ $service->name ?? 'Layanan Event' }}</span>
                        </div>
                        <div class="pt-0.5">
                            <button type="button" onclick="openDetailModal('{{ $groupId }}')" class="text-xs font-semibold text-purple-600 hover:text-purple-800 underline">Lihat Detail Menu</button>
                        </div>
                    </div>

                    <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2.5 pt-2 sm:pt-0 border-t border-gray-100 sm:border-t-0">
                        <p class="text-lg font-bold text-gray-900 sm:text-right">Rp {{ number_format($groupSubtotal, 0, ',', '.') }}</p>
                        <div class="flex items-center gap-2 sm:justify-end">
                            <button type="button" onclick="openEditEventModal('{{ $groupId }}')" class="px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                Edit
                            </button>
                            <button type="button" onclick="confirmDeleteEvent('{{ $groupId }}', {{ $packageItem->id ?? $groupItems->first()->id }}, true)" class="px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
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
                    $service = $groupItems->first()->cateringService;
                    $groupSubtotal = $groupItems->sum(fn($c) => $c->subtotal);
                @endphp
                <div class="py-6 first:pt-4 last:pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4" id="event-card-{{ $groupId }}">
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="font-bold text-gray-900 text-base sm:text-lg">Custom Menu</span>
                            <span class="text-gray-400 font-medium">·</span>
                            <span class="text-sm text-gray-500 font-medium">{{ $service->name ?? 'Layanan Event' }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-600">
                            {{ $menuItems->count() }} Menu Dipilih
                        </p>
                        <div class="pt-0.5">
                            <button type="button" onclick="openDetailModal('{{ $groupId }}')" class="text-xs font-semibold text-purple-600 hover:text-purple-800 underline">Lihat Detail</button>
                        </div>
                    </div>

                    <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2.5 pt-2 sm:pt-0 border-t border-gray-100 sm:border-t-0">
                        <p class="text-lg font-bold text-gray-900 sm:text-right">Rp {{ number_format($groupSubtotal, 0, ',', '.') }}</p>
                        <div class="flex items-center gap-2 sm:justify-end">
                            <button type="button" onclick="openEditEventModal('{{ $groupId }}')" class="px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                Edit
                            </button>
                            <button type="button" onclick="confirmDeleteEvent('{{ $groupId }}', {{ $customHeader?->id ?? $groupItems->first()->id }}, false)" class="px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Ringkasan Belanja & Tombol Checkout Global untuk Semua Event -->
            <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border-purple-100 flex flex-col sm:flex-row justify-between items-center gap-4 sticky bottom-4 z-10">
                <div>
                    <!-- <h4 class="font-bold text-gray-900 text-lg">Ringkasan Belanja Event</h4> -->
                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-600">
                        <!-- <span>Subtotal: <strong class="text-gray-900">Rp {{ number_format($eventGroups->flatten()->sum('subtotal'), 0, ',', '.') }}</strong></span>
                        <span>•</span> -->
                        <span>Total: <strong class="text-gray-600 font-bold text-lg">Rp {{ number_format($eventGroups->flatten()->sum('subtotal'), 0, ',', '.') }}</strong></span>
                    </div>
                </div>
                <a href="{{ route('customer.event.checkout.show', 'all') }}"
                    class="w-full sm:w-auto text-center px-8 py-4 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold rounded-xl hover:shadow-lg transition-all text-base">
                    Checkout 
                </a>
            </div>
        @endif
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Daily Edit (tetap seperti lama) --}}
{{-- =============================== --}}
@php
    $allDailyCartsJson = $dailyGroups->flatten()->keyBy('id')->map(function($c) {
        $basePrice = $c->product ? (float) $c->product->price : ($c->customOption ? (float) $c->customOption->price : 0);
        $name = $c->product->name ?? ($c->customOption->name ?? 'Item');
        $serviceId = $c->product->catering_service_id ?? ($c->customOption->catering_service_id ?? null);
        return [
            'id' => $c->id,
            'name' => $name,
            'price' => $basePrice,
            'quantity' => $c->quantity,
            'service_id' => $serviceId,
            'product_id' => $c->product_id,
            'extras' => $c->extras ?? [],
            'update_url' => route('customer.cart.update', $c->id),
        ];
    });
@endphp

<div id="dailyEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col transform transition-all">
        <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-orange-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Ubah Pesanan</span>
            </h3>
            <button type="button" onclick="closeDailyEditModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6">
            <form id="dailyEditForm" action="" method="POST">
                @csrf @method('PUT')
                <div class="mb-6 bg-orange-50/50 rounded-xl p-4">
                    <h4 id="dailyModalProductName" class="font-bold text-gray-900"></h4>
                    <p id="dailyModalProductPrice" class="text-orange-600 font-semibold text-sm"></p>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Porsi *</label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeDailyQty(-1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg transition-colors">−</button>
                        <input type="number" name="quantity" id="dailyModalQty" value="1" min="1" class="w-20 text-center px-3 py-2 rounded-xl border border-gray-200 font-semibold text-gray-900 focus:border-orange-400 focus:ring-2 focus:ring-orange-100" onchange="updateDailyModalTotal()">
                        <button type="button" onclick="changeDailyQty(1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg transition-colors">+</button>
                    </div>
                </div>
                <div id="dailyModalExtrasLoading" class="text-sm text-gray-500 py-2 hidden">Memuat opsi tambahan...</div>
                <div id="dailyModalExtrasContainer" class="mb-6 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Extra Tambahan (Opsional)</label>
                    <div id="dailyModalExtrasList" class="space-y-2"></div>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-4 border border-orange-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Estimasi Total</span>
                        <span id="dailyModalTotal" class="text-xl font-bold text-orange-600">Rp 0</span>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-6 border-t border-gray-100 flex-shrink-0 flex gap-3">
            <button type="button" onclick="closeDailyEditModal()" class="flex-1 px-6 py-3.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors text-sm">Batal</button>
            <button type="button" onclick="document.getElementById('dailyEditForm').submit()" class="flex-1 px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all text-sm">Update</button>
        </div>
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Event Edit --}}
{{-- =============================== --}}
@php
    $eventGroupsJson = [];
    foreach($eventGroups as $gId => $gItems) {
        $pkgItem = $gItems->firstWhere('item_type', 'package');
        $customHeader = $gItems->firstWhere('item_type', 'custom_header');
        $setsQty = $customHeader ? (int) $customHeader->quantity : ($pkgItem ? (int) $pkgItem->quantity : 1);
        if ($setsQty <= 0) $setsQty = 1;

        $eventGroupsJson[$gId] = [
            'group_id' => $gId,
            'service_name' => $gItems->first()->cateringService->name ?? 'Layanan Event',
            'package_name' => $pkgItem?->cateringPackage?->name,
            'package_quantity' => $pkgItem ? (int) $pkgItem->quantity : 1,
            'package_portions_per_unit' => $pkgItem?->cateringPackage?->total_portions ?? 0,
            'package_price' => (float) ($pkgItem?->cateringPackage?->price ?? 0),
            'sets_quantity' => $setsQty,
            'serving_name' => $gItems->first()->servingType?->name,
            'notes' => $customHeader?->notes ?? ($pkgItem?->notes ?? ($gItems->first()->notes ?? '')),
            'subtotal' => $gItems->sum(fn($c) => $c->subtotal),
            'catering_service_id' => $gItems->first()->catering_service_id,
            'catering_package_id' => $pkgItem?->catering_package_id,
            'serving_type_id' => $customHeader?->serving_type_id ?? ($pkgItem?->serving_type_id ?? ($gItems->first()->serving_type_id ?? null)),
            'is_package' => $pkgItem !== null,
            'min_portion' => $gItems->first()->cateringService->min_portion ?? 1,
            'max_portion' => $gItems->first()->cateringService->max_portion ?? 1000,
            'update_url' => route('customer.event.cart.update', $gId),
            'items' => $gItems->filter(fn($c) => in_array($c->item_type, ['package_item', 'custom_menu', 'addition']))->map(fn($c) => [
                'custom_option_id' => $c->custom_option_id,
                'quantity' => $c->quantity,
                'item_type' => $c->item_type,
                'name' => $c->customOption->name ?? 'Item',
                'price' => (float) ($c->customOption->price ?? 0),
                'subtotal' => (float) ($c->subtotal ?? 0),
            ])->values()->toArray(),
        ];
    }
@endphp

{{-- =============================== --}}
{{-- MODAL: Event Detail --}}
{{-- =============================== --}}
<div id="detailEventModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <div>
                <span id="detailModalBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700"></span>
                <h3 id="detailModalTitle" class="text-lg font-bold text-gray-900 mt-1"></h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6 space-y-4">
            <div>
                <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Daftar Menu</h5>
                <div id="detailModalList" class="divide-y divide-gray-100 border border-gray-100 rounded-xl px-4 py-2"></div>
            </div>
            <div id="detailModalServingSection" class="hidden">
                <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Penyajian</h5>
                <p id="detailModalServing" class="text-sm font-medium text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-100"></p>
            </div>
            <div id="detailModalNotesSection" class="hidden">
                <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Catatan</h5>
                <p id="detailModalNotes" class="text-sm text-gray-700 italic bg-gray-50 p-3 rounded-xl border border-gray-100"></p>
            </div>
        </div>
        <div class="p-6 border-t border-gray-100 flex-shrink-0 flex justify-between items-center bg-gray-50 rounded-b-2xl">
            <span class="text-sm text-gray-600">Total Harga Group</span>
            <span id="detailModalTotal" class="text-lg font-bold text-purple-600"></span>
        </div>
    </div>
</div>

<div id="editEventModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-orange-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit Pesanan Event</span>
            </h3>
            <button type="button" onclick="closeEditEventModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6">
            <form id="editEventForm" action="" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="catering_service_id" id="editEventServiceId">
                <input type="hidden" name="catering_package_id" id="editEventPackageId">

                {{-- Info Header --}}
                <div id="editEventHeader" class="bg-purple-50 rounded-xl p-4 mb-6">
                    <p class="font-bold text-gray-900" id="editEventTitle"></p>
                    <p class="text-sm text-purple-600" id="editEventType"></p>
                </div>

                {{-- Menu Items (akan di-generate JS) --}}
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3" id="editEventMenuHeading">Menu</h4>
                    <div id="editEventMenuList" class="space-y-2"></div>

                    {{-- Porsi Indicator (hanya untuk custom menu) --}}
                    <div id="editCustomPortionIndicator" class="mt-4 p-4 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-between" style="display:none;">
                        <span class="text-base font-bold text-gray-900">Total Porsi : <span id="editCustomPortionsDisplay">0</span> / <span id="editCustomMaxTarget">100</span></span>
                    </div>
                </div>

                {{-- Extra --}}
                <div id="editEventExtrasSection" class="mb-6" style="display:none;">
                    <h4 class="font-semibold text-gray-800 mb-3">Extra</h4>
                    <div id="editEventExtrasList" class="space-y-2"></div>
                </div>

                {{-- Penyajian --}}
                <div id="editEventServingSection" class="mb-6" style="display:none;">
                    <h4 class="font-semibold text-gray-800 mb-3">Penyajian</h4>
                    <div id="editEventServingList" class="grid grid-cols-3 gap-3"></div>
                </div>

                {{-- Total --}}
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-4 border border-orange-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Harga</span>
                        <span id="editEventTotal" class="text-xl font-bold text-orange-600">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-sm text-gray-500">Total Porsi</span>
                        <span class="font-semibold text-gray-700">
                            <span id="editEventPortions">0</span> Porsi
                        </span>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-6 border-t border-gray-100 flex-shrink-0 flex gap-3">
            <button type="button" onclick="closeEditEventModal()" class="flex-1 px-6 py-3.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors text-sm">Batal</button>
            <button type="button" id="editEventSubmitBtn" onclick="submitEditEventForm()" class="flex-1 px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all text-sm">Update</button>
        </div>
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Delete Confirmation --}}
{{-- =============================== --}}
<div id="deleteConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 text-red-500 flex items-center justify-center">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Pesanan?</h3>
        <p class="text-gray-500 mb-6">Apakah Anda ingin menghapus pesanan ini?</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">Tidak</button>
            <form id="deleteEventForm" action="" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-6 py-3 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600 transition-colors">Ya</button>
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
        document.getElementById('content-daily').style.display = tab === 'daily' ? '' : 'none';
        document.getElementById('content-event').style.display = tab === 'event' ? '' : 'none';

        const tabDaily = document.getElementById('tab-daily');
        const tabEvent = document.getElementById('tab-event');
        tabDaily.classList.toggle('border-orange-500', tab === 'daily');
        tabDaily.classList.toggle('text-orange-600', tab === 'daily');
        tabDaily.classList.toggle('border-transparent', tab !== 'daily');
        tabDaily.classList.toggle('text-gray-500', tab !== 'daily');
        tabEvent.classList.toggle('border-orange-500', tab === 'event');
        tabEvent.classList.toggle('text-orange-600', tab === 'event');
        tabEvent.classList.toggle('border-transparent', tab !== 'event');
        tabEvent.classList.toggle('text-gray-500', tab !== 'event');
    }

    // =======================
    // DAILY EDIT MODAL
    // =======================
    const dailyCartsData = @json($allDailyCartsJson);
    let currentDailyCart = null;
    let dailyAvailableExtras = [];

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    function openDailyEditModal(cartId) {
        currentDailyCart = dailyCartsData[cartId];
        if (!currentDailyCart) return;

        document.getElementById('dailyEditForm').action = currentDailyCart.update_url;
        document.getElementById('dailyModalProductName').textContent = currentDailyCart.name;
        document.getElementById('dailyModalProductPrice').textContent = formatRupiah(currentDailyCart.price);
        document.getElementById('dailyModalQty').value = currentDailyCart.quantity;

        document.getElementById('dailyModalExtrasList').innerHTML = '';
        document.getElementById('dailyModalExtrasContainer').classList.add('hidden');
        document.getElementById('dailyModalExtrasLoading').classList.remove('hidden');

        let extrasUrl = `/api/service/${currentDailyCart.service_id}/custom-options`;
        if (currentDailyCart.product_id) {
            extrasUrl = `/api/product/${currentDailyCart.product_id}/extras`;
        }

        fetch(extrasUrl)
            .then(res => res.json())
            .then(data => {
                dailyAvailableExtras = data.filter(opt => opt.type === 'extra');
                document.getElementById('dailyModalExtrasLoading').classList.add('hidden');

                if (dailyAvailableExtras.length > 0) {
                    let html = '';
                    dailyAvailableExtras.forEach(extra => {
                        let extraInCart = currentDailyCart.extras.find(e => parseInt(e.id) === parseInt(extra.id));
                        let isChecked = extraInCart ? 'checked' : '';
                        let extraQty = extraInCart ? extraInCart.qty : 0;
                        let disabledState = extraInCart ? '' : 'disabled';
                        let containerClasses = extraInCart ? '' : 'opacity-0 pointer-events-none';

                        html += `
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl hover:bg-orange-50 transition-colors">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input type="checkbox" name="extras[${extra.id}][id]" value="${extra.id}" data-price="${extra.price}" id="daily_extra_cb_${extra.id}" onchange="toggleDailyExtra(${extra.id})" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-400 daily-extra-checkbox" ${isChecked}>
                                    <span class="text-sm font-medium text-gray-700">${extra.name}</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-orange-600">+${formatRupiah(extra.price)}</span>
                                    <div class="flex items-center gap-1 transition-opacity duration-200 ${containerClasses}" id="daily_extra_qty_container_${extra.id}">
                                        <button type="button" onclick="changeDailyExtraQty(${extra.id}, -1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition-colors">−</button>
                                        <input type="text" inputmode="numeric" name="extras[${extra.id}][qty]" id="daily_extra_qty_${extra.id}" value="${extraQty}" oninput="manualDailyExtraQty(${extra.id})" onchange="manualDailyExtraQty(${extra.id})" class="w-10 text-center bg-transparent text-sm font-bold text-gray-900 focus:outline-none" ${disabledState}>
                                        <button type="button" onclick="changeDailyExtraQty(${extra.id}, 1)" class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition-colors">+</button>
                                    </div>
                                </div>
                            </div>`;
                    });
                    document.getElementById('dailyModalExtrasList').innerHTML = html;
                    document.getElementById('dailyModalExtrasContainer').classList.remove('hidden');
                }
                updateDailyModalTotal();
            })
            .catch(err => {
                console.error("Gagal memuat extras", err);
                document.getElementById('dailyModalExtrasLoading').classList.add('hidden');
            });

        updateDailyModalTotal();
        document.getElementById('dailyEditModal').style.display = 'flex';
    }

    function closeDailyEditModal() {
        document.getElementById('dailyEditModal').style.display = 'none';
    }

    function changeDailyQty(delta) {
        const input = document.getElementById('dailyModalQty');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        input.value = val;
        updateDailyModalTotal();
    }

    function toggleDailyExtra(id) {
        const cb = document.getElementById('daily_extra_cb_' + id);
        const qtyContainer = document.getElementById('daily_extra_qty_container_' + id);
        const qtyInput = document.getElementById('daily_extra_qty_' + id);
        if (cb.checked) {
            qtyContainer.classList.remove('opacity-0', 'pointer-events-none');
            qtyInput.value = 1;
            qtyInput.disabled = false;
        } else {
            qtyContainer.classList.add('opacity-0', 'pointer-events-none');
            qtyInput.value = 0;
            qtyInput.disabled = true;
        }
        updateDailyModalTotal();
    }

    function changeDailyExtraQty(id, delta) {
        const cb = document.getElementById('daily_extra_cb_' + id);
        if (!cb || !cb.checked) return;
        const input = document.getElementById('daily_extra_qty_' + id);
        let val = parseInt(input.value) || 0;
        val += delta;
        if (val < 1) { cb.checked = false; toggleDailyExtra(id); } else { input.value = val; updateDailyModalTotal(); }
    }

    function manualDailyExtraQty(id) {
        const cb = document.getElementById('daily_extra_cb_' + id);
        if (!cb || !cb.checked) return;
        const input = document.getElementById('daily_extra_qty_' + id);
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) { if (input.value === "") return; val = 1; input.value = 1; }
        updateDailyModalTotal();
    }

    function updateDailyModalTotal() {
        if (!currentDailyCart) return;
        const qty = parseInt(document.getElementById('dailyModalQty').value) || 1;
        let total = currentDailyCart.price * qty;
        document.querySelectorAll('.daily-extra-checkbox:checked').forEach(cb => {
            const extraId = cb.value;
            const extraQty = parseInt(document.getElementById('daily_extra_qty_' + extraId).value) || 0;
            total += parseFloat(cb.getAttribute('data-price')) * extraQty;
        });
        document.getElementById('dailyModalTotal').textContent = formatRupiah(total);
    }

    document.getElementById('dailyEditModal').addEventListener('click', function(e) { if (e.target === this) closeDailyEditModal(); });

    // =======================
    // EVENT EDIT MODAL
    // =======================
    const eventGroupsData = @json($eventGroupsJson);
    let editingGroupId = null;
    let editServiceOptions = [];
    let initialEditEventSnapshot = null;

    function openEditEventModal(groupId) {
        editingGroupId = groupId;
        initialEditEventSnapshot = null;
        const group = eventGroupsData[groupId];
        if (!group) return;

        document.getElementById('editEventForm').action = group.update_url;
        document.getElementById('editEventServiceId').value = group.catering_service_id;
        document.getElementById('editEventPackageId').value = group.catering_package_id || '';

        fetch(`/api/service/${group.catering_service_id}/custom-options`)
            .then(res => res.json())
            .then(options => {
                editServiceOptions = options;
                renderEditEventModal(group, options);
            });

        document.getElementById('editEventModal').style.display = 'flex';
    }

    function renderEditEventModal(group, options) {
        const menus = options.filter(o => o.type === 'menu');
        const extras = options.filter(o => o.type === 'extra');
        const servings = options.filter(o => o.type === 'serving_type');

        if (group.is_package) {
            document.getElementById('editEventTitle').textContent = group.package_name || 'Paket';
            document.getElementById('editEventType').textContent = group.service_name || 'Layanan Event';
            const currentQty = group.package_quantity || 1;
            document.getElementById('editEventMenuList').innerHTML = `
                <div class="p-4 bg-orange-50/60 rounded-xl border border-orange-100 flex items-center justify-between">
                    <div>
                        <label class="block font-bold text-gray-900 text-base">Jumlah Paket</label>
                        
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changePackageEditQty(-1)" class="w-10 h-10 rounded-xl bg-white hover:bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-bold text-lg shadow-sm transition-colors">−</button>
                        <input type="number" name="quantity" id="editPackageQtyInput" value="${currentQty}" min="1" class="w-20 text-center py-2 px-3 rounded-xl border border-gray-200 font-bold text-gray-900 focus:border-orange-400 focus:ring-2 focus:ring-orange-100" onchange="recalcEditEvent()">
                        <button type="button" onclick="changePackageEditQty(1)" class="w-10 h-10 rounded-xl bg-white hover:bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-bold text-lg shadow-sm transition-colors">+</button>
                    </div>
                </div>
            `;
            document.getElementById('editEventExtrasSection').style.display = 'none';
            document.getElementById('editEventServingSection').style.display = 'none';
            const heading = document.getElementById('editEventMenuHeading');
            if (heading) heading.style.display = 'none';
            document.getElementById('editCustomPortionIndicator').style.display = 'none';
            initialEditEventSnapshot = getEditEventSnapshot();
            recalcEditEvent();
        } else {
            document.getElementById('editEventTitle').textContent = 'Custom Menu';
            document.getElementById('editEventType').textContent = group.service_name || 'Layanan Event';
            let menuHtml = '';
            menus.forEach((menu, idx) => {
                const existingItem = group.items.find(i => i.custom_option_id == menu.id && i.item_type === 'custom_menu');
                const qty = existingItem ? existingItem.quantity : 0;
                const checked = qty > 0 ? 'checked' : '';
                const opacityClass = qty > 0 ? '' : 'opacity-0 pointer-events-none';

                menuHtml += `
                <div class="flex items-center justify-between p-3 border rounded-xl mb-2">
                    <label class="flex items-center gap-3 cursor-pointer flex-1">
                        <input type="checkbox" id="edit_menu_cb_${idx}" data-idx="${idx}" data-id="${menu.id}" data-price="${menu.price}"
                            class="w-5 h-5 rounded border-gray-300 text-orange-500 edit-menu-checkbox" ${checked} onchange="toggleEditMenu(${idx}, ${menu.id})">
                        <div>
                            <span class="font-medium text-gray-900">${menu.name}</span>
                            <p class="text-xs text-orange-600">Rp ${Number(menu.price).toLocaleString('id-ID')} / porsi</p>
                        </div>
                    </label>
                    <div class="flex items-center gap-2 transition-opacity ${opacityClass}" id="edit_menu_qty_container_${idx}">
                        <button type="button" onclick="changeEditEventQty(${idx}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                        <input type="number" name="items[${idx}][quantity]" id="edit_event_qty_${idx}" value="${qty}" min="0"
                            class="w-16 text-center border rounded-lg py-1 font-semibold edit-event-qty" data-price="${menu.price}" data-type="custom_menu" oninput="recalcEditEvent()" onchange="recalcEditEvent()">
                        <button type="button" onclick="changeEditEventQty(${idx}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 edit-menu-plus-btn">+</button>
                    </div>
                    <input type="hidden" name="items[${idx}][custom_option_id]" value="${menu.id}" class="edit-event-item-field" ${qty > 0 ? '' : 'disabled'}>
                    <input type="hidden" name="items[${idx}][item_type]" value="custom_menu" class="edit-event-item-field" ${qty > 0 ? '' : 'disabled'}>
                </div>`;
            });
            document.getElementById('editEventMenuList').innerHTML = menuHtml;

            let extraStartIdx = menus.length;
            if (extras.length > 0) {
                document.getElementById('editEventExtrasSection').style.display = 'block';
                let extraHtml = '';
                extras.forEach((extra, i) => {
                    const eIdx = extraStartIdx + i;
                    const existingExtra = group.items.find(item => item.custom_option_id == extra.id && item.item_type === 'addition');
                    const eQty = existingExtra ? existingExtra.quantity : 0;
                    const eChecked = eQty > 0 ? 'checked' : '';
                    const opacityClass = eQty > 0 ? '' : 'opacity-0 pointer-events-none';

                    extraHtml += `
                    <div class="flex items-center justify-between p-3 border rounded-xl mb-2">
                        <label class="flex items-center gap-3 cursor-pointer flex-1">
                            <input type="checkbox" id="edit_extra_cb_${eIdx}" data-idx="${eIdx}" data-id="${extra.id}" data-price="${extra.price}"
                                class="w-5 h-5 rounded border-gray-300 text-orange-500 edit-extra-checkbox" ${eChecked} onchange="toggleEditExtra(${eIdx}, ${extra.id})">
                            <div>
                                <span class="font-medium text-gray-900">${extra.name}</span>
                                <p class="text-xs text-orange-600">+Rp ${Number(extra.price).toLocaleString('id-ID')}</p>
                            </div>
                        </label>
                        <div class="flex items-center gap-2 transition-opacity ${opacityClass}" id="edit_extra_qty_container_${eIdx}">
                            <button type="button" onclick="changeEditEventQty(${eIdx}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                            <input type="number" name="items[${eIdx}][quantity]" id="edit_event_qty_${eIdx}" value="${eQty}" min="0"
                                class="w-16 text-center border rounded-lg py-1 font-semibold edit-event-qty" data-price="${extra.price}" data-type="addition" oninput="recalcEditEvent()" onchange="recalcEditEvent()">
                            <button type="button" onclick="changeEditEventQty(${eIdx}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                        </div>
                        <input type="hidden" name="items[${eIdx}][custom_option_id]" value="${extra.id}" class="edit-event-item-field" ${eQty > 0 ? '' : 'disabled'}>
                        <input type="hidden" name="items[${eIdx}][item_type]" value="addition" class="edit-event-item-field" ${eQty > 0 ? '' : 'disabled'}>
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
                    <label class="relative cursor-pointer">
                        <input type="radio" name="serving_type_id" value="${s.id}" class="peer sr-only" ${checked} onchange="recalcEditEvent()">
                        <div class="p-3 text-center border-2 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-colors">
                            <span class="font-medium text-gray-900">${s.name}</span>
                        </div>
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
    }

    function changePackageEditQty(delta) {
        const input = document.getElementById('editPackageQtyInput');
        if (!input) return;
        let val = parseInt(input.value) || 1;
        val = Math.max(1, val + delta);
        input.value = val;
        recalcEditEvent();
    }

    function toggleEditMenu(idx, menuId) {
        const cb = document.getElementById('edit_menu_cb_' + idx);
        const container = document.getElementById('edit_menu_qty_container_' + idx);
        const input = document.getElementById('edit_event_qty_' + idx);
        const hiddens = document.querySelectorAll(`input[name="items[${idx}][custom_option_id]"], input[name="items[${idx}][item_type]"]`);

        if (cb.checked) {
            container.classList.remove('opacity-0', 'pointer-events-none');
            input.value = 1;
            hiddens.forEach(h => h.disabled = false);
        } else {
            container.classList.add('opacity-0', 'pointer-events-none');
            input.value = 0;
            hiddens.forEach(h => h.disabled = true);
        }
        recalcEditEvent();
    }

    function toggleEditExtra(idx, extraId) {
        const cb = document.getElementById('edit_extra_cb_' + idx);
        const container = document.getElementById('edit_extra_qty_container_' + idx);
        const input = document.getElementById('edit_event_qty_' + idx);
        const hiddens = document.querySelectorAll(`input[name="items[${idx}][custom_option_id]"], input[name="items[${idx}][item_type]"]`);

        if (cb.checked) {
            container.classList.remove('opacity-0', 'pointer-events-none');
            input.value = 1;
            hiddens.forEach(h => h.disabled = false);
        } else {
            container.classList.add('opacity-0', 'pointer-events-none');
            input.value = 0;
            hiddens.forEach(h => h.disabled = true);
        }
        recalcEditEvent();
    }

    function changeEditEventQty(idx, delta) {
        const input = document.getElementById('edit_event_qty_' + idx);
        if (!input) return;
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        input.value = val;
        recalcEditEvent();
    }

    function getEditEventSnapshot() {
        if (!editingGroupId) return '';
        const group = eventGroupsData[editingGroupId];
        if (!group) return '';

        if (group.is_package) {
            const qty = parseInt(document.getElementById('editPackageQtyInput')?.value) || group.package_quantity || 1;
            return 'package:' + qty;
        }

        const items = [];
        document.querySelectorAll('#editEventForm input.edit-event-qty').forEach(input => {
            const name = input.name || '';
            const match = name.match(/items\[(\d+)\]/);
            if (!match) return;
            const idx = match[1];

            let active = true;
            const menuCb = document.getElementById('edit_menu_cb_' + idx);
            const extraCb = document.getElementById('edit_extra_cb_' + idx);
            if (menuCb && !menuCb.checked) active = false;
            if (extraCb && !extraCb.checked) active = false;

            if (active) {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    const optIdInput = document.querySelector(`#editEventForm input[name="items[${idx}][custom_option_id]"]`);
                    if (optIdInput) {
                        items.push(optIdInput.value + ':' + qty);
                    }
                }
            }
        });
        items.sort();

        const servingRadio = document.querySelector('#editEventForm input[name="serving_type_id"]:checked');
        const servingVal = servingRadio ? servingRadio.value : '';

        return 'custom:' + items.join('|') + '||' + servingVal;
    }

    function recalcEditEvent() {
        const group = eventGroupsData[editingGroupId];
        if (!group) return;

        const btn = document.getElementById('editEventSubmitBtn');
        let isValid = true;

        if (group.is_package) {
            const qty = parseInt(document.getElementById('editPackageQtyInput')?.value) || 1;
            const pkgPrice = group.package_price || 0;
            const totalPortions = (group.package_portions_per_unit || 0) * qty;

            document.getElementById('editEventTotal').textContent = formatRupiah(pkgPrice * qty);
            document.getElementById('editEventPortions').textContent = totalPortions;
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } else {
            let totalPrice = 0;
            let totalPortions = 0;
            const maxPortion = group.max_portion || 1000;

            document.querySelectorAll('.edit-event-qty').forEach(input => {
                const name = input.name || '';
                const match = name.match(/items\[(\d+)\]/);
                if (!match) return;
                const idx = match[1];

                let active = true;
                const menuCb = document.getElementById('edit_menu_cb_' + idx);
                const extraCb = document.getElementById('edit_extra_cb_' + idx);
                if (menuCb && !menuCb.checked) active = false;
                if (extraCb && !extraCb.checked) active = false;

                if (active) {
                    let qty = parseInt(input.value) || 0;
                    const price = parseFloat(input.dataset.price) || 0;
                    const type = input.dataset.type;

                    if (type === 'custom_menu') {
                        if (totalPortions + qty > maxPortion) {
                            qty = Math.max(0, maxPortion - totalPortions);
                            input.value = qty;
                        }
                        totalPortions += qty;
                        totalPrice += price * qty;
                    } else if (type === 'addition') {
                        totalPrice += price * qty;
                    }
                }
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

            const hasServing = document.querySelector('#editEventForm input[name="serving_type_id"]:checked');
            const isServingRequired = document.querySelector('#editEventForm input[name="serving_type_id"]') !== null;

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
        }
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

        const form = document.getElementById('editEventForm');
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
            const badge = i.item_type === 'package_item' ? '<span class="text-xs text-green-600 font-medium">(Termasuk)</span>' : '';
            const priceStr = i.item_type === 'package_item' ? '' : `Rp ${Number(i.subtotal).toLocaleString('id-ID')}`;
            html += `
            <div class="py-2.5 flex items-center justify-between text-sm">
                <div>
                    <span class="font-medium text-gray-900">${i.name}</span>
                    <span class="text-gray-500 text-xs ml-1">× ${i.quantity}</span>
                    ${badge}
                </div>
                <span class="font-medium text-gray-700">${priceStr}</span>
            </div>`;
        });
        document.getElementById('detailModalList').innerHTML = html || '<p class="text-sm text-gray-500 py-2">Tidak ada menu</p>';
        
        if (group.serving_name) {
            document.getElementById('detailModalServingSection').classList.remove('hidden');
            document.getElementById('detailModalServing').textContent = group.serving_name;
        } else {
            document.getElementById('detailModalServingSection').classList.add('hidden');
        }
        
        if (group.notes) {
            document.getElementById('detailModalNotesSection').classList.remove('hidden');
            document.getElementById('detailModalNotes').textContent = group.notes;
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
                fetch(`/dashboard/cart/${cartId}`, {
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

            const newDaily = doc.getElementById('content-daily');
            const newEvent = doc.getElementById('content-event');
            const newTabDaily = doc.getElementById('tab-daily');
            const newTabEvent = doc.getElementById('tab-event');

            if (newDaily && document.getElementById('content-daily')) {
                document.getElementById('content-daily').innerHTML = newDaily.innerHTML;
            }
            if (newEvent && document.getElementById('content-event')) {
                document.getElementById('content-event').innerHTML = newEvent.innerHTML;
            }
            if (newTabDaily && document.getElementById('tab-daily')) {
                document.getElementById('tab-daily').innerHTML = newTabDaily.innerHTML;
            }
            if (newTabEvent && document.getElementById('tab-event')) {
                document.getElementById('tab-event').innerHTML = newTabEvent.innerHTML;
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
                if (text.includes('const dailyCartsData =')) {
                    const matchDaily = text.match(/const dailyCartsData = (\{[\s\S]*?\});\s*(?:let|const|function|var|\n|$)/);
                    if (matchDaily && matchDaily[1]) {
                        try {
                            const parsed = JSON.parse(matchDaily[1]);
                            for (let k in dailyCartsData) delete dailyCartsData[k];
                            Object.assign(dailyCartsData, parsed);
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
@endsection
