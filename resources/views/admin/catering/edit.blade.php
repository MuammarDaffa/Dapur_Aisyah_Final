@extends('layouts.admin')
@section('title', 'Edit Katering')
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('admin.catering.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Katering</a>
        </div>

        <form action="{{ route('admin.catering.update', $catering->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Edit Informasi Katering</h3>
                </div>
                <div class="card-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Katering <span class="text-danger">*</span></label>
                        <input type="text" name="nama" required value="{{ old('nama', $catering->nama) }}" class="form-control">
                        @error('nama')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

            
                {{-- Tipe Katering (Dikunci) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Tipe Katering</label>
                        <input type="text" class="form-control bg-light" value="{{ ucfirst($catering->tipe) }}" readonly>
                        {{-- Hidden input agar data tipe tetap terkirim ke controller --}}
                        <input type="hidden" name="tipe" value="{{ $catering->tipe }}">
                        <small class="text-muted">Tipe katering tidak dapat diubah setelah layanan dibuat.</small>
                    </div>


                     @if($catering->isAcara())
                        {{-- Kapasitas (Hanya Acara) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kapasitas Total <span class="text-danger">*</span></label>
                            <input type="number" name="kapasitas_total" value="{{ old('kapasitas_total', $catering->kapasitas_total) }}" min="1" step="1" class="form-control">
                            @error('kapasitas_total')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        
                        {{-- Minimal Porsi (Hanya Acara) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Minimal Porsi Pemesanan <span class="text-danger">*</span></label>
                            <input type="number" name="minimal_porsi" value="{{ old('minimal_porsi', $catering->minimal_porsi) }}" min="1" step="1" class="form-control">
                            @error('minimal_porsi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    {{-- Status --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="status" value="1" {{ old('status', $catering->status) ? 'checked' : '' }} id="statusCheck">
                        <label class="form-check-label fw-bold" for="statusCheck">
                            Aktif
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Perbarui Katering</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection
