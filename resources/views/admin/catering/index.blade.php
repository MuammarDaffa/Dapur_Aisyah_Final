@extends('layouts.admin')

@section('title', 'Kelola Katering')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Daftar Katering</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.catering.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Katering
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama Katering</th>
                                <th class="text-center">Tipe</th>
                                
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($caterings as $c)
                            <tr>
                                <td class="align-middle">
                                    <span class="fw-bold">{{ $c->nama }}</span>
                                    
                                </td>
                                <td class="align-middle text-center">
                                    @if($c->isHarian())
                                        <span class="badge text-bg-info">Harian</span>
                                    @elseif($c->isAcara())
                                        <span class="badge text-bg-purple" style="background-color: #6f42c1; color: white;">Acara</span>
                                    @else
                                        <span class="badge text-bg-secondary">-</span>
                                    @endif
                                </td>
                                
                                <td class="align-middle text-center">
                                    <span class="badge {{ $c->status ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $c->status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
<td class="align-middle text-center">
                                    <span class="text-muted fst-italic">Dalam Pengembangan</span>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fa-solid fa-utensils fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted fw-medium mb-1">Belum ada katering.</p>
                                    <small class="text-muted">Buat layanan katering pertama Anda.</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($caterings->hasPages())
            <div class="card-footer">
                {{ $caterings->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
