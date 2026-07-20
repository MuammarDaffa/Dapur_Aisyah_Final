@extends('layouts.admin')
@section('title', 'Edit Ongkos Kirim')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.ongkos_kirim.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Ongkos Kirim</a>
        </div>

        <form action="{{ route('admin.ongkos_kirim.update', $shipping) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Data Wilayah dan Tarif</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kecamatan <span class="text-danger">*</span></label>
                        <select name="kecamatan_id" id="kecamatan_id" class="form-select" required>
                            <option value="">Pilih Kecamatan...</option>
                            @foreach($kecamatan as $kecamatan)
                                <option value="{{ $kecamatan->id }}" {{ $shipping->kecamatan_id == $kecamatan->id ? 'selected' : '' }}>
                                    {{ $kecamatan->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('kecamatan_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kelurahan <span class="text-danger">*</span></label>
                        <select name="desa_id" id="desa_id" class="form-select" required>
                            <option value="">Pilih Kelurahan...</option>
                        </select>
                        @error('desa_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ongkos Kirim (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="cost" class="form-control rupiah-input" value="{{ old('cost', number_format($shipping->cost, 0, '', '')) }}" required>
                        @error('cost')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Ongkos Kirim</button>
                    <a href="{{ route('admin.ongkos_kirim.index') }}" class="btn btn-default">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const districtSelect = document.getElementById('kecamatan_id');
        const villageSelect = document.getElementById('desa_id');
        const oldVillageId = '{{ $shipping->desa_id }}';

        function loadVillages(districtId, selectedVillageId = null) {
            villageSelect.innerHTML = '<option value="">Memuat...</option>';
            villageSelect.disabled = true;

            if (districtId) {
                fetch(`/api/kecamatan/${districtId}/desa`)
                    .then(response => response.json())
                    .then(data => {
                        villageSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
                        data.forEach(desa => {
                            const selected = selectedVillageId == desa.id ? 'selected' : '';
                            villageSelect.innerHTML += `<option value="${desa.id}" ${selected}>${desa.name}</option>`;
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
