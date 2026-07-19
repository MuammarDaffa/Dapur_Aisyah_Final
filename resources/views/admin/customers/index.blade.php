@extends('layouts.admin')
@section('title', 'Data Pelanggan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Daftar Pelanggan</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Total Pesanan</th>
                                <th>Terdaftar Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $c)
                            <tr>
                                <td class="align-middle fw-medium">{{ $c->name }}</td>
                                <td class="align-middle">{{ $c->email }}</td>
                                <td class="align-middle">
                                    <span class="badge text-bg-info">{{ $c->orders_count }} Pesanan</span>
                                </td>
                                <td class="align-middle text-muted">{{ $c->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada pelanggan terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($customers->hasPages())
            <div class="card-footer">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
