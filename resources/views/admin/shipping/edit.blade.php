@extends('layouts.admin')

@section('title', 'Edit Ongkos Kirim')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="d-flex align-items-center g-3">
        <a href="{{ route('admin.shipping.index') }}" class="text-secondary hover:text-secondary">
            <svg style="width: 24px; height: 24px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="fs-3 fw-bold text-secondary">Edit Ongkos Kirim</h2>
            <p class="fs-6 text-secondary mt-1">Perbarui biaya pengiriman untuk {{ $shipping->district->name ?? 'kecamatan' }}</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded shadow-md border border border-secondary p-6">
        <form action="{{ route('admin.shipping.update', $shipping) }}" method="POST" class="d-flex flex-column gap-3">
            @csrf
            @method('PUT')

            {{-- Kecamatan --}}
            <div>
                <label for="district_id" class="d-block fs-6 fw-bold text-secondary mb-1.5">Kecamatan <span class="text-danger">*</span></label>
                <select name="district_id" id="district_id" required
                        class="form-select w-100 border border-secondary rounded shadow-sm focus:border border-primary">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ old('district_id', $shipping->district_id) == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
                @error('district_id')
                    <p class="fs-6 text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Biaya --}}
            <div>
                <label for="cost" class="d-block fs-6 fw-bold text-secondary mb-1.5">Biaya (Rp) <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <span class="position-absolute /2 -translate-y-1/2 text-secondary fs-6 fw-medium">Rp</span>
                    <input type="text" name="cost" id="cost" value="{{ old('cost', $shipping->cost) }}" required
                           class="w-100 ps-10 border border-secondary rounded shadow-sm focus:border border-primary rupiah-input">
                </div>
                @error('cost')
                    <p class="fs-6 text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="notes" class="d-block fs-6 fw-bold text-secondary mb-1.5">Catatan</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Catatan opsional..."
                          class="form-control w-100 border border-secondary rounded shadow-sm focus:border border-primary">{{ old('notes', $shipping->notes) }}</textarea>
                @error('notes')
                    <p class="fs-6 text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="d-flex align-items-center g-3 pt-4 border-t border border-secondary">
                <button type="submit"
                        class="hover: hover: text-white px-6 py-2.5 rounded fw-bold shadow-md hover:shadow">
                    Perbarui
                </button>
                <a href="{{ route('admin.shipping.index') }}"
                   class="text-secondary hover:text-secondary px-6 py-2.5 rounded border border border-secondary hover:bg-light fw-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
