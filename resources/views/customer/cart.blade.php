@extends('layouts.app')
@section('title', 'Keranjang Belanja')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">🛒 Keranjang <span class="text-orange-500">Belanja</span></h2>

    {{-- Tab Navigation --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button type="button" onclick="switchTab('daily')" id="tab-daily"
            class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'daily' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            🍱 Daily
            @if($dailyCarts->isNotEmpty())
                <span class="ml-1 px-2 py-0.5 bg-orange-100 text-orange-600 rounded-full text-xs font-bold">{{ $dailyCarts->count() }}</span>
            @endif
        </button>
        <button type="button" onclick="switchTab('event')" id="tab-event"
            class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'event' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            🎉 Event
            @if($eventGroups->isNotEmpty())
                <span class="ml-1 px-2 py-0.5 bg-purple-100 text-purple-600 rounded-full text-xs font-bold">{{ $eventGroups->count() }}</span>
            @endif
        </button>
    </div>

    {{-- =============================== --}}
    {{-- TAB DAILY --}}
    {{-- =============================== --}}
    <div id="content-daily" style="{{ $activeTab !== 'daily' ? 'display:none' : '' }}">
        @if($dailyCarts->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
                <p class="text-5xl mb-4">🍱</p>
                <p class="text-gray-500 font-medium mb-4">Keranjang harian Anda kosong</p>
                <a href="{{ route('customer.products') }}" class="px-6 py-3 bg-orange-500 text-white font-medium rounded-full hover:bg-orange-600 transition-colors">Lihat Menu →</a>
            </div>
        @else
            <div class="space-y-4 mb-6">
                @foreach($dailyCarts as $cart)
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
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 cart-item" data-base-price="{{ $basePrice }}" data-extras-price="{{ $extrasPrice }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4 flex-1">
                            <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">
                                @if($cart->product && $cart->product->image)
                                    <img src="{{ Storage::url($cart->product->image) }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover rounded-xl">
                                @else
                                    🍛
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900">{{ $cart->product->name ?? ($cart->customOption->name ?? 'Item') }}</h4>
                                @if($extrasList->isNotEmpty())
                                    <div class="text-sm text-gray-600 mt-1 mb-1">
                                        <span class="font-medium">Extra:</span>
                                        <ul class="list-disc pl-4 mt-0.5 space-y-0.5 text-xs">
                                            @foreach($extrasList as $ex)
                                                <li>{{ $ex->name }} {{ $ex->qty }}x (+Rp {{ number_format($ex->price, 0, ',', '.') }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <p class="text-sm text-orange-500">{{ $cart->product->cateringService->name ?? '' }}</p>
                                <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($basePrice, 0, ',', '.') }} / porsi</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-3 ml-4">
                            <p class="text-lg font-bold text-gray-900 item-subtotal">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</p>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm font-medium text-gray-500">{{ $cart->quantity }} porsi</span>
                                <button type="button" onclick="openDailyEditModal({{ $cart->id }})" class="px-4 py-2 bg-orange-100 text-orange-600 text-xs font-bold rounded-lg hover:bg-orange-200 transition-colors">Ubah Pesanan</button>
                            </div>
                            <form action="{{ route('customer.cart.destroy', $cart) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex items-center gap-1 px-3 py-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Daily Summary -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600">Subtotal (<span id="item-count">{{ $dailyCarts->count() }}</span> item)</span>
                    <span class="text-xl font-bold text-gray-900" id="cart-total">Rp {{ number_format($dailySubtotal, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('customer.checkout') }}" class="block w-full text-center px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                    Lanjut ke Checkout →
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
                <p class="text-5xl mb-4">🎉</p>
                <p class="text-gray-500 font-medium mb-4">Belum ada pesanan event</p>
                <a href="{{ route('landing') }}#services" class="px-6 py-3 bg-purple-500 text-white font-medium rounded-full hover:bg-purple-600 transition-colors">Pilih Layanan Event →</a>
            </div>
        @else
            <div class="space-y-6">
                @foreach($eventGroups as $groupId => $groupItems)
                @php
                    $packageItem = $groupItems->firstWhere('item_type', 'package');
                    $menuItems = $groupItems->whereIn('item_type', ['package_item', 'custom_menu']);
                    $additionItems = $groupItems->where('item_type', 'addition');
                    $service = $groupItems->first()->cateringService;
                    $servingType = $groupItems->first()->servingType;
                    $isPackage = $packageItem !== null;
                    $groupSubtotal = $groupItems->sum(fn($c) => $c->subtotal);
                    $totalPortions = $menuItems->sum('quantity');
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-purple-200 overflow-hidden" id="event-card-{{ $groupId }}">
                    {{-- Card Header --}}
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b border-purple-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🎉</span>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $service->name ?? 'Layanan Event' }}</h4>
                                    <p class="text-sm font-medium {{ $isPackage ? 'text-orange-600' : 'text-blue-600' }}">
                                        Jenis: {{ $isPackage ? 'Paket' : 'Custom' }}
                                        @if($isPackage && $packageItem->cateringPackage)
                                            — {{ $packageItem->cateringPackage->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($groupSubtotal, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ $totalPortions }} Porsi</p>
                            </div>
                        </div>
                    </div>

                    {{-- Card Body: Menu List --}}
                    <div class="px-6 py-4">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Menu:</h5>
                        <div class="divide-y divide-gray-100">
                            @foreach($menuItems as $cart)
                            <div class="py-2 flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    @if($cart->item_type === 'package_item')
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Paket</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">Menu</span>
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $cart->customOption->name ?? 'Item' }}</span>
                                    <span class="text-gray-500">({{ $cart->quantity }})</span>
                                </div>
                                <span class="font-medium {{ $cart->item_type === 'package_item' ? 'text-green-600' : 'text-orange-600' }}">
                                    @if($cart->item_type === 'package_item')
                                        Termasuk
                                    @else
                                        Rp {{ number_format($cart->subtotal, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Extras --}}
                        @if($additionItems->isNotEmpty())
                        <h5 class="text-sm font-semibold text-gray-700 mt-3 mb-2">Extra:</h5>
                        <div class="divide-y divide-gray-100">
                            @foreach($additionItems as $cart)
                            <div class="py-2 flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full">Extra</span>
                                    <span class="font-medium text-gray-900">{{ $cart->customOption->name ?? 'Item' }}</span>
                                    <span class="text-gray-500">({{ $cart->quantity }})</span>
                                </div>
                                <span class="font-medium text-orange-600">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Penyajian --}}
                        @if($servingType)
                        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2 text-sm">
                            <span class="text-gray-600">🍲 Penyajian:</span>
                            <span class="font-medium text-gray-900">{{ $servingType->name }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Card Footer: Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-3">
                        <a href="{{ route('customer.event.checkout.show', $groupId) }}"
                            class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all text-sm">
                            Checkout
                        </a>
                        <button type="button" onclick="openEditEventModal('{{ $groupId }}')"
                            class="flex-1 text-center px-4 py-2.5 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-colors text-sm">
                            Edit
                        </button>
                        <button type="button" onclick="confirmDeleteEvent('{{ $groupId }}', {{ $groupItems->first()->id }})"
                            class="flex-1 text-center px-4 py-2.5 bg-red-100 text-red-600 font-semibold rounded-xl hover:bg-red-200 transition-colors text-sm">
                            Hapus
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Daily Edit (tetap seperti lama) --}}
{{-- =============================== --}}
@php
    $allDailyCartsJson = $dailyCarts->keyBy('id')->map(function($c) {
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
            <h3 class="text-lg font-bold text-gray-900">📝 Ubah Pesanan</h3>
            <button type="button" onclick="closeDailyEditModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6">
            <form id="dailyEditForm" action="" method="POST">
                @csrf @method('PUT')
                <div class="flex items-center space-x-4 mb-6 bg-orange-50/50 rounded-xl p-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">🍛</div>
                    <div>
                        <h4 id="dailyModalProductName" class="font-bold text-gray-900"></h4>
                        <p id="dailyModalProductPrice" class="text-orange-600 font-semibold text-sm"></p>
                    </div>
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
        $eventGroupsJson[$gId] = [
            'group_id' => $gId,
            'catering_service_id' => $gItems->first()->catering_service_id,
            'catering_package_id' => $pkgItem?->catering_package_id,
            'serving_type_id' => $gItems->first()->serving_type_id,
            'is_package' => $pkgItem !== null,
            'update_url' => route('customer.event.cart.update', $gId),
            'items' => $gItems->filter(fn($c) => in_array($c->item_type, ['package_item', 'custom_menu', 'addition']))->map(fn($c) => [
                'custom_option_id' => $c->custom_option_id,
                'quantity' => $c->quantity,
                'item_type' => $c->item_type,
                'name' => $c->customOption->name ?? 'Item',
                'price' => (float) ($c->customOption->price ?? 0),
            ])->values()->toArray(),
        ];
    }
@endphp

<div id="editEventModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">📝 Edit Pesanan Event</h3>
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
                    <h4 class="font-semibold text-gray-800 mb-3">Menu</h4>

                    {{-- Porsi Indicator (untuk paket) --}}
                    <div id="editPkgPortionIndicator" class="mb-3 p-3 rounded-xl border" style="display:none;">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-semibold text-gray-700">Target: <span id="editPkgTarget" class="text-orange-600">0</span></span>
                            <span class="text-sm font-semibold text-gray-700">Dipilih: <span id="editPkgSelected" class="text-blue-600">0</span></span>
                        </div>
                        <p id="editPkgMsg" class="text-sm font-medium"></p>
                    </div>

                    <div id="editEventMenuList" class="space-y-2"></div>
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
                        <span id="editEventPortions" class="font-semibold text-gray-700">0</span>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-6 border-t border-gray-100 flex-shrink-0 flex gap-3">
            <button type="button" onclick="closeEditEventModal()" class="flex-1 px-6 py-3.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors text-sm">Batal</button>
            <button type="button" id="editEventSubmitBtn" onclick="document.getElementById('editEventForm').submit()" class="flex-1 px-6 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:shadow-lg transition-all text-sm">Update</button>
        </div>
    </div>
</div>

{{-- =============================== --}}
{{-- MODAL: Delete Confirmation --}}
{{-- =============================== --}}
<div id="deleteConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
        <div class="text-5xl mb-4">⚠️</div>
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
        document.getElementById('dailyModalProductPrice').textContent = formatRupiah(currentDailyCart.price) + ' / porsi';
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

    function openEditEventModal(groupId) {
        editingGroupId = groupId;
        const group = eventGroupsData[groupId];
        if (!group) return;

        document.getElementById('editEventForm').action = group.update_url;
        document.getElementById('editEventServiceId').value = group.catering_service_id;
        document.getElementById('editEventPackageId').value = group.catering_package_id || '';

        // Load service options via API
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

        // Header
        document.getElementById('editEventTitle').textContent = group.is_package ? 'Pesanan Paket Event' : 'Pesanan Custom Event';
        document.getElementById('editEventType').textContent = group.is_package ? 'Mode Paket' : 'Mode Custom';

        // Package portion indicator
        if (group.is_package && group.catering_package_id) {
            document.getElementById('editPkgPortionIndicator').style.display = 'block';
            // Fetch package details for total_portions
            fetch(`/api/package/${group.catering_package_id}/details`)
                .then(res => res.json())
                .then(pkg => {
                    document.getElementById('editPkgTarget').textContent = pkg.total_portions;
                    recalcEditEvent();
                });
        } else {
            document.getElementById('editPkgPortionIndicator').style.display = 'none';
        }

        // Menu list
        let menuHtml = '';
        menus.forEach((menu, idx) => {
            const existingItem = group.items.find(i => i.custom_option_id == menu.id && (i.item_type === 'package_item' || i.item_type === 'custom_menu'));
            const qty = existingItem ? existingItem.quantity : 0;
            const itemType = group.is_package ? 'package_item' : 'custom_menu';
            const checked = qty > 0 ? 'checked' : '';

            if (group.is_package) {
                // Paket: semua menu ditampilkan dengan qty input
                menuHtml += `
                <div class="flex items-center justify-between p-3 border rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900">${menu.name}</p>
                        <p class="text-xs ${group.is_package ? 'text-green-600' : 'text-orange-600'} font-medium">${group.is_package ? 'Termasuk paket' : 'Rp ' + Number(menu.price).toLocaleString('id-ID') + ' / porsi'}</p>
                        <input type="hidden" name="items[${idx}][custom_option_id]" value="${menu.id}" class="edit-event-item-field">
                        <input type="hidden" name="items[${idx}][item_type]" value="${itemType}" class="edit-event-item-field">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="changeEditEventQty(${idx}, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">−</button>
                        <input type="number" name="items[${idx}][quantity]" id="edit_event_qty_${idx}" value="${qty}" min="0"
                            class="w-16 text-center border rounded-lg py-1 font-semibold edit-event-qty" data-price="${menu.price}" data-type="${itemType}" onchange="recalcEditEvent()">
                        <button type="button" onclick="changeEditEventQty(${idx}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                    </div>
                </div>`;
            } else {
                // Custom: checkbox + qty
                const opacityClass = qty > 0 ? '' : 'opacity-0 pointer-events-none';
                menuHtml += `
                <div class="flex items-center justify-between p-3 border rounded-xl">
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
                            class="w-16 text-center border rounded-lg py-1 font-semibold edit-event-qty" data-price="${menu.price}" data-type="custom_menu" onchange="recalcEditEvent()">
                        <button type="button" onclick="changeEditEventQty(${idx}, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600">+</button>
                    </div>
                    <input type="hidden" name="items[${idx}][custom_option_id]" value="${menu.id}" class="edit-event-item-field" ${qty > 0 ? '' : 'disabled'}>
                    <input type="hidden" name="items[${idx}][item_type]" value="custom_menu" class="edit-event-item-field" ${qty > 0 ? '' : 'disabled'}>
                </div>`;
            }
        });
        document.getElementById('editEventMenuList').innerHTML = menuHtml;

        // Extras
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
                <div class="flex items-center justify-between p-3 border rounded-xl">
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
                            class="w-16 text-center border rounded-lg py-1 font-semibold edit-event-qty" data-price="${extra.price}" data-type="addition" onchange="recalcEditEvent()">
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

        // Penyajian
        if (servings.length > 0) {
            document.getElementById('editEventServingSection').style.display = 'block';
            let servingHtml = '';
            servings.forEach(s => {
                const checked = group.serving_type_id == s.id ? 'checked' : '';
                servingHtml += `
                <label class="relative cursor-pointer">
                    <input type="radio" name="serving_type_id" value="${s.id}" class="peer sr-only" ${checked}>
                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-colors">
                        <span class="font-medium text-gray-900">${s.name}</span>
                    </div>
                </label>`;
            });
            document.getElementById('editEventServingList').innerHTML = servingHtml;
        } else {
            document.getElementById('editEventServingSection').style.display = 'none';
        }

        recalcEditEvent();
    }

    function toggleEditMenu(idx, menuId) {
        const cb = document.getElementById('edit_menu_cb_' + idx);
        const container = document.getElementById('edit_menu_qty_container_' + idx);
        const input = document.getElementById('edit_event_qty_' + idx);
        // Get the hidden fields
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

    function recalcEditEvent() {
        let totalPrice = 0;
        let totalPortions = 0;

        const group = eventGroupsData[editingGroupId];
        if (!group) return;

        // If package mode, price = package price (fixed)
        if (group.is_package && group.catering_package_id) {
            // Package price is from the package item
            const pkgItem = group.items.find(i => false); // We need to get it from API
            // For now, we calc from qty inputs
        }

        document.querySelectorAll('.edit-event-qty').forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            const type = input.dataset.type;

            if (type === 'package_item') {
                totalPortions += qty;
                // Harga sudah termasuk paket, tidak ditambah
            } else if (type === 'custom_menu') {
                totalPortions += qty;
                totalPrice += price * qty;
            } else if (type === 'addition') {
                totalPrice += price * qty;
            }
        });

        // If package, add package price
        if (group.is_package && group.catering_package_id) {
            fetch(`/api/package/${group.catering_package_id}/details`)
                .then(res => res.json())
                .then(pkg => {
                    const pkgPrice = parseFloat(pkg.price) || 0;
                    document.getElementById('editEventTotal').textContent = formatRupiah(pkgPrice + totalPrice);

                    // Validate portions for package
                    const target = pkg.total_portions;
                    document.getElementById('editPkgTarget').textContent = target;
                    document.getElementById('editPkgSelected').textContent = totalPortions;
                    const msg = document.getElementById('editPkgMsg');
                    const btn = document.getElementById('editEventSubmitBtn');
                    const indicator = document.getElementById('editPkgPortionIndicator');

                    if (totalPortions < target) {
                        indicator.className = 'mb-3 p-3 rounded-xl border border-yellow-200 bg-yellow-50';
                        msg.textContent = `⚠️ Kurang ${target - totalPortions} porsi`;
                        msg.className = 'text-sm font-medium text-yellow-700';
                        btn.disabled = true;
                        btn.classList.add('opacity-50');
                    } else if (totalPortions > target) {
                        indicator.className = 'mb-3 p-3 rounded-xl border border-red-200 bg-red-50';
                        msg.textContent = `❌ Kelebihan ${totalPortions - target} porsi`;
                        msg.className = 'text-sm font-medium text-red-700';
                        btn.disabled = true;
                        btn.classList.add('opacity-50');
                    } else {
                        indicator.className = 'mb-3 p-3 rounded-xl border border-green-200 bg-green-50';
                        msg.textContent = `✅ Porsi tepat!`;
                        msg.className = 'text-sm font-medium text-green-700';
                        btn.disabled = false;
                        btn.classList.remove('opacity-50');
                    }
                });
        } else {
            document.getElementById('editEventTotal').textContent = formatRupiah(totalPrice);
            // Custom validation: min 30 portions
            const btn = document.getElementById('editEventSubmitBtn');
            if (totalPortions < 30) {
                btn.disabled = true;
                btn.classList.add('opacity-50');
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50');
            }
        }

        document.getElementById('editEventPortions').textContent = totalPortions;
    }

    function closeEditEventModal() {
        document.getElementById('editEventModal').style.display = 'none';
    }

    document.getElementById('editEventModal').addEventListener('click', function(e) { if (e.target === this) closeEditEventModal(); });

    // =======================
    // DELETE CONFIRMATION
    // =======================
    function confirmDeleteEvent(groupId, cartId) {
        document.getElementById('deleteEventForm').action = '{{ url("dashboard/cart") }}/' + cartId;
        document.getElementById('deleteConfirmModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }

    document.getElementById('deleteConfirmModal').addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });
</script>
@endpush
@endsection
