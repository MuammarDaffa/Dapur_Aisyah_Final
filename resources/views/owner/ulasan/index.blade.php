@extends('layouts.owner')
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
                                <th>Pelanggan</th>
                                <th>Komentar</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ulasan as $r)
                            <tr>
                                <td class="align-middle">{{ $r->user->name ?? '-' }}</td>
                                <td class="align-middle text-wrap">{{ $r->komentar }}</td>
                                <td class="align-middle text-muted">{{ $r->created_at->format('d M Y') }}</td>
                                <td class="align-middle text-center">
                                    <form action="{{ route('owner.ulasan.destroy', $r->id) }}" method="POST" class="d-inline" id="delete-form-{{ $r->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('delete-form-{{ $r->id }}', 'Hapus ulasan ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada ulasan.</td>
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
