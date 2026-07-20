@extends('layouts.admin')
@section('title', 'Ulasan Pelanggan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Daftar Ulasan</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Rating</th>
                                <th>Komentar</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ulasan as $r)
                            <tr>
                                <td class="align-middle"><a href="{{ route('admin.pesanan.show', $r->pesanan) }}" class="text-decoration-none fw-bold">{{ $r->pesanan->nomor_pesanan }}</a></td>
                                <td class="align-middle">{{ $r->user->name ?? '-' }}</td>
                                <td class="align-middle">
                                    <div class="text-warning">
                                        @for($i=1; $i<=5; $i++)
                                            @if($i <= $r->rating)
                                                <i class="fa-solid fa-star"></i>
                                            @else
                                                <i class="fa-regular fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                <td class="align-middle text-wrap">{{ $r->comment }}</td>
                                <td class="align-middle text-muted">{{ $r->created_at->format('d M Y') }}</td>
                                <td class="align-middle text-center">
                                    <form action="{{ route('admin.ulasan.destroy', $r) }}" method="POST" class="d-inline" onsubmit="event.preventDefault(); confirmDeleteForm(this, 'Hapus ulasan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada ulasan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($ulasan->hasPages())
            <div class="card-footer">
                {{ $ulasan->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
