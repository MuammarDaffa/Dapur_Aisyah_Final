@extends('layouts.admin')
@section('title', 'Ongkos Kirim')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Daftar Ongkos Kirim</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.ongkos_kirim.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Ongkos Kirim
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th class="text-end">Ongkos Kirim (Rp)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ongkosKirim as $s)
                            <tr>
                                <td class="align-middle">{{ $s->kecamatan->name ?? '-' }}</td>
                                <td class="align-middle">{{ $s->desa->name ?? '-' }}</td>
                                <td class="align-middle text-end text-success fw-bold">{{ number_format($s->cost, 0, ',', '.') }}</td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.ongkos_kirim.edit', $s) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-edit"></i> Edit</a>
                                        <form action="{{ route('admin.ongkos_kirim.destroy', $s) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this);">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data ongkos kirim.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
