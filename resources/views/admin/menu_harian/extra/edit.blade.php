@extends('layouts.admin')
@section('title', 'Edit Extra: ' . $extra->nama_extra)
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.menu-harian.extra.index', $menuHarian) }}" class="btn btn-default"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title fw-bold">Edit Extra</h3>
            </div>
            <form action="{{ route('admin.menu-harian.extra.update', [$menuHarian, $extra]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Extra <span class="text-danger">*</span></label>
                        <input type="text" name="nama_extra" class="form-control @error('nama_extra') is-invalid @enderror" value="{{ old('nama_extra', $extra->nama_extra) }}" required>
                        @error('nama_extra')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $extra->harga) }}" min="0" required>
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-info text-white"><i class="fa-solid fa-save"></i> Update Extra</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
