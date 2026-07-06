@extends('layouts.admin')
@section('title', 'Atur Jadwal Menu: ' . $catering->name)
@section('content')
<div class="space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.catering.show', $catering) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-orange-500 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Detail Katering</span>
        </a>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
            <span>Layanan Daily Katering</span>
        </span>
    </div>

    {{-- Title & Description --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Editor Jadwal Menu Mingguan</h1>
                <p class="text-sm text-gray-500 mt-1">Atur rentang tanggal berlaku dan pilih produk menu harian untuk layanan <span class="font-semibold text-gray-800">{{ $catering->name }}</span>.</p>
            </div>
            @if($schedule)
            <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-green-50 border border-green-200 rounded-xl text-xs font-semibold text-green-800">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Jadwal Aktif Tersimpan</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium flex items-center gap-3">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
        <div class="font-bold mb-1 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Terdapat kesalahan pada input:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 ml-5">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Main Form --}}
    <form id="scheduleForm" action="{{ route('admin.menu-periods.store', $catering) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Top Section: Rentang Tanggal --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Tentukan Rentang Tanggal</span>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai *</label>
                    <input type="date" name="start_date" id="input_start_date"
                           value="{{ old('start_date', $schedule?->start_date?->format('Y-m-d') ?? '') }}"
                           required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-orange-100 focus:border-orange-500 transition-all font-medium text-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai *</label>
                    <input type="date" name="end_date" id="input_end_date"
                           value="{{ old('end_date', $schedule?->end_date?->format('Y-m-d') ?? '') }}"
                           required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-orange-100 focus:border-orange-500 transition-all font-medium text-gray-800">
                </div>
                <div>
                    <button type="button" id="btn_generate"
                            class="w-full px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-orange-500 transition-all shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Buat Jadwal</span>
                    </button>
                </div>
            </div>

            <div class="mt-4 p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-xs leading-relaxed text-blue-800">
                    <span class="font-bold">Catatan:</span> Tombol <span class="font-semibold">Buat Jadwal</span> hanya digunakan untuk membentuk daftar tanggal yang akan diisi menu. Data belum disimpan ke database sampai admin menekan tombol <span class="font-semibold">Simpan Jadwal</span> di bawah.
                </p>
            </div>
        </div>

        {{-- Table Section: Daftar Menu Harian --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50/80 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Daftar Menu Harian</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih produk menu yang akan disajikan untuk setiap tanggal di bawah ini.</p>
                </div>
                <div id="badge_count" class="hidden px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">
                    0 Hari Terpilih
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 text-xs uppercase text-gray-500 tracking-wider border-b">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-semibold w-12">No</th>
                            <th class="px-6 py-3.5 text-left font-semibold w-56">Tanggal</th>
                            <th class="px-6 py-3.5 text-left font-semibold w-40">Nama Hari</th>
                            <th class="px-6 py-3.5 text-left font-semibold">Menu Katering (Produk) *</th>
                        </tr>
                    </thead>
                    <tbody id="schedule_tbody" class="divide-y divide-gray-100">
                        {{-- Rows generated by JavaScript --}}
                    </tbody>
                </table>
            </div>

            {{-- Empty State in Table before Generate --}}
            <div id="table_empty_state" class="p-12 text-center text-gray-500">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h4 class="font-bold text-gray-800 mb-1">Daftar Tanggal Belum Dibuat</h4>
                <p class="text-xs text-gray-500 max-w-sm mx-auto">Silakan pilih <span class="font-semibold">Tanggal Mulai</span> dan <span class="font-semibold">Tanggal Selesai</span> di atas, lalu klik tombol <span class="font-semibold text-gray-700">Buat Jadwal</span>.</p>
            </div>

            {{-- Footer / Submit Action --}}
            <div id="table_footer" class="hidden px-6 py-4 bg-gray-50/80 border-t flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500">
                    Pastikan seluruh tanggal telah dipilihkan produk menunya sebelum menyimpan.
                </div>
                <button type="submit"
                        class="w-full sm:w-auto px-8 py-3 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Jadwal Menu</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
@php
    $productsJson = $products->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'price_label' => 'Rp ' . number_format($p->price, 0, ',', '.')
        ];
    })->values()->all();

    $scheduleItems = $schedule && $schedule->items ? $schedule->items->map(function($i) {
        return [
            'menu_date' => $i->menu_date->format('Y-m-d'),
            'product_id' => $i->product_id
        ];
    })->all() : [];

    $oldItemsJson = old('items', $scheduleItems);
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    const products = {!! json_encode($productsJson) !!};
    const oldItems = {!! json_encode($oldItemsJson) !!};

    const inputStart = document.getElementById('input_start_date');
    const inputEnd = document.getElementById('input_end_date');
    const btnGenerate = document.getElementById('btn_generate');
    const tbody = document.getElementById('schedule_tbody');
    const emptyState = document.getElementById('table_empty_state');
    const tableFooter = document.getElementById('table_footer');
    const badgeCount = document.getElementById('badge_count');

    const indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function formatIndonesianDate(dateStr) {
        const parts = dateStr.split('-');
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        const dateObj = new Date(year, month, day);
        
        const dayName = indonesianDays[dateObj.getDay()];
        const monthName = indonesianMonths[month];
        
        return {
            dayName: dayName,
            formattedDate: `${day < 10 ? '0' + day : day} ${monthName} ${year}`,
            dateObj: dateObj
        };
    }

    function renderTable(itemsData) {
        tbody.innerHTML = '';
        
        if (!itemsData || itemsData.length === 0) {
            emptyState.classList.remove('hidden');
            tableFooter.classList.add('hidden');
            badgeCount.classList.add('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        tableFooter.classList.remove('hidden');
        tableFooter.classList.add('flex');
        badgeCount.classList.remove('hidden');
        badgeCount.textContent = `${itemsData.length} Hari Terpilih`;

        let optionsHtml = '<option value="">-- Pilih Menu Katering --</option>';
        products.forEach(p => {
            optionsHtml += `<option value="${p.id}">${p.name} — ${p.price_label}</option>`;
        });

        itemsData.forEach((item, index) => {
            const dateInfo = formatIndonesianDate(item.menu_date);
            const row = document.createElement('tr');
            row.className = 'hover:bg-orange-50/30 transition-colors';
            
            // Build dropdown with selected value
            let selectHtml = `<select name="items[${index}][product_id]" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-orange-100 focus:border-orange-500 bg-white">`;
            selectHtml += `<option value="">-- Pilih Menu Katering --</option>`;
            products.forEach(p => {
                const selected = (p.id == item.product_id) ? 'selected' : '';
                selectHtml += `<option value="${p.id}" ${selected}>${p.name} — ${p.price_label}</option>`;
            });
            selectHtml += `</select>`;

            row.innerHTML = `
                <td class="px-6 py-4 font-semibold text-gray-500">${index + 1}</td>
                <td class="px-6 py-4 font-bold text-gray-900">
                    ${dateInfo.formattedDate}
                    <input type="hidden" name="items[${index}][menu_date]" value="${item.menu_date}">
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">
                        ${dateInfo.dayName}
                    </span>
                </td>
                <td class="px-6 py-4">
                    ${selectHtml}
                </td>
            `;

            tbody.appendChild(row);
        });
    }

    // Handle Generate Button Click
    btnGenerate.addEventListener('click', function() {
        const startVal = inputStart.value;
        const endVal = inputEnd.value;

        if (!startVal || !endVal) {
            alert('Silakan pilih Tanggal Mulai dan Tanggal Selesai terlebih dahulu.');
            return;
        }

        if (endVal < startVal) {
            alert('Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.');
            return;
        }

        // Store current selections from DOM so we can preserve menu choices if date overlaps
        const currentSelections = {};
        const existingInputs = tbody.querySelectorAll('input[type="hidden"]');
        existingInputs.forEach(input => {
            const dateVal = input.value;
            const selectEl = input.closest('tr').querySelector('select');
            if (selectEl && selectEl.value) {
                currentSelections[dateVal] = selectEl.value;
            }
        });

        // Calculate sequential dates
        const partsStart = startVal.split('-');
        const partsEnd = endVal.split('-');
        let currDate = new Date(parseInt(partsStart[0], 10), parseInt(partsStart[1], 10) - 1, parseInt(partsStart[2], 10));
        const endDate = new Date(parseInt(partsEnd[0], 10), parseInt(partsEnd[1], 10) - 1, parseInt(partsEnd[2], 10));

        const newItems = [];
        let maxDays = 366; // Sanity check
        let count = 0;

        while (currDate <= endDate && count < maxDays) {
            const year = currDate.getFullYear();
            const month = String(currDate.getMonth() + 1).padStart(2, '0');
            const day = String(currDate.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;

            newItems.push({
                menu_date: dateStr,
                product_id: currentSelections[dateStr] || ''
            });

            currDate.setDate(currDate.getDate() + 1);
            count++;
        }

        renderTable(newItems);
    });

    // On load, if there are existing items (from DB or validation old input), render them!
    if (oldItems && oldItems.length > 0) {
        renderTable(oldItems);
    }
});
</script>
@endpush
@endsection
