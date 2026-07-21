@extends('layouts.admin')
@section('title', 'Kelola Extra: ' . $menuHarian->nama_menu)
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.catering.show', $menuHarian->layanan_katering_id) }}" class="btn btn-default"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title fw-bold">Tambah Extra</h3>
            </div>
            <form action="{{ route('admin.menu-harian.extra.store', $menuHarian) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Extra <span class="text-danger">*</span></label>
                        <input type="text" name="nama_extra" class="form-control @error('nama_extra') is-invalid @enderror" value="{{ old('nama_extra') }}" required>
                        @error('nama_extra')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga') }}" min="0" required>
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Extra</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title fw-bold">Daftar Extra: {{ $menuHarian->nama_menu }} (Hari {{ $menuHarian->hari }})</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama Extra</th>
                            <th>Harga</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuHarian->extras as $extra)
                        <tr>
                            <td class="align-middle fw-medium">{{ $extra->nama_extra }}</td>
                            <td class="align-middle">Rp {{ number_format($extra->harga, 0, ',', '.') }}</td>
                            <td class="align-middle text-center">
                                <a href="{{ route('admin.menu-harian.extra.edit', [$menuHarian, $extra]) }}" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-edit"></i> Edit</a>
                                <form action="{{ route('admin.menu-harian.extra.destroy', [$menuHarian, $extra]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this.closest('form'), 'Hapus extra ini?')" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada extra untuk menu ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
