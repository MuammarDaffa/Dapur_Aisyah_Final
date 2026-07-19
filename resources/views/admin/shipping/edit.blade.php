@extends('layouts.admin')
@section('title', 'Edit Ongkos Kirim')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.shipping.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Ongkos Kirim</a>
        </div>

        <form action="{{ route('admin.shipping.update', $shipping) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Data Wilayah dan Tarif</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kecamatan <span class="text-danger">*</span></label>
                        <select name="district_id" id="district_id" class="form-select" required>
                            <option value="">Pilih Kecamatan...</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ $shipping->district_id == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kelurahan <span class="text-danger">*</span></label>
                        <select name="village_id" id="village_id" class="form-select" required>
                            <option value="">Pilih Kelurahan...</option>
                        </select>
                        @error('village_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ongkos Kirim (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="cost" class="form-control rupiah-input" value="{{ old('cost', number_format($shipping->cost, 0, '', '')) }}" required>
                        @error('cost')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Ongkos Kirim</button>
                    <a href="{{ route('admin.shipping.index') }}" class="btn btn-default">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const districtSelect = document.getElementById('district_id');
        const villageSelect = document.getElementById('village_id');
        const oldVillageId = '{{ $shipping->village_id }}';

        function loadVillages(districtId, selectedVillageId = null) {
            villageSelect.innerHTML = '<option value="">Memuat...</option>';
            villageSelect.disabled = true;

            if (districtId) {
                fetch(`/api/districts/${districtId}/villages`)
                    .then(response => response.json())
                    .then(data => {
                        villageSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                        data.forEach(village => {
                            const selected = selectedVillageId == village.id ? 'selected' : '';
                            villageSelect.innerHTML += `<option value="${village.id}" ${selected}>${village.name}</option>`;
                        });
                        villageSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        villageSelect.innerHTML = '<option value="">Gagal memuat kelurahan</option>';
                    });
            } else {
                villageSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                villageSelect.disabled = true;
            }
        }

        districtSelect.addEventListener('change', function() {
            loadVillages(this.value);
        });

        if (districtSelect.value) {
            loadVillages(districtSelect.value, oldVillageId);
        }
    });
</script>
@endpush
@endsection
