@extends('layouts.admin')

@section('title', 'Edit Ongkos Kirim')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.shipping.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Ongkos Kirim</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui biaya pengiriman untuk {{ $shipping->district->name ?? 'kecamatan' }}</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <form action="{{ route('admin.shipping.update', $shipping) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Kecamatan --}}
            <div>
                <label for="district_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                <select name="district_id" id="district_id" required
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ old('district_id', $shipping->district_id) == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
                @error('district_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Biaya --}}
            <div>
                <label for="cost" class="block text-sm font-semibold text-gray-700 mb-1.5">Biaya (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $shipping->cost) }}" required min="0"
                           class="w-full pl-10 border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 transition-colors">
                </div>
                @error('cost')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Catatan opsional..."
                          class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-orange-500 focus:border-orange-500 transition-colors">{{ old('notes', $shipping->notes) }}</textarea>
                @error('notes')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-6 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
                    Perbarui
                </button>
                <a href="{{ route('admin.shipping.index') }}"
                   class="text-gray-500 hover:text-gray-700 px-6 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 transition-all font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
