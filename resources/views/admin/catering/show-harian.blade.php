@extends('layouts.admin')
@section('title', 'Detail Katering: ' . $catering->name)
@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.catering.index') }}" class="btn btn-default"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Katering</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-info mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold">Kelola Menu Harian</h3>
            </div>
            <form action="{{ route('admin.menu-harian.update-batch', $catering) }}" method="POST">
                @csrf
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Hari</th>
                                    <th style="width: 15%;">Tanggal</th>
                                    <th style="width: 25%;">Nama Menu</th>
                                    <th style="width: 15%;">Harga / Porsi</th>
                                    <th style="width: 10%;">Stok Awal</th>
                                    <th style="width: 10%;">Sisa Stok</th>
                                    <th style="width: 15%;" class="text-center">Extra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                                @endphp
                                @foreach($hariList as $index => $hari)
                                    @php
                                        $menuData = $menus->where('hari', $hari)->first();
                                    @endphp
                                    <tr>
                                        <td class="align-middle fw-bold">
                                            {{ $hari }}
                                            <input type="hidden" name="menus[{{ $index }}][hari]" value="{{ $hari }}">
                                        </td>
                                        <td class="align-middle">
                                            <input type="date" name="menus[{{ $index }}][tanggal]" 
                                                   value="{{ old('menus.'.$index.'.tanggal', $menuData ? $menuData->tanggal?->format('Y-m-d') : '') }}" 
                                                   class="form-control form-control-sm @error('menus.'.$index.'.tanggal') is-invalid @enderror">
                                            @error('menus.'.$index.'.tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" name="menus[{{ $index }}][nama_menu]" 
                                                   value="{{ old('menus.'.$index.'.nama_menu', $menuData ? $menuData->nama_menu : '') }}" 
                                                   class="form-control form-control-sm @error('menus.'.$index.'.nama_menu') is-invalid @enderror" 
                                                   placeholder="Misal: Nasi Ayam">
                                            @error('menus.'.$index.'.nama_menu')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="align-middle">
                                            <input type="number" name="menus[{{ $index }}][harga]" 
                                                   value="{{ old('menus.'.$index.'.harga', $menuData ? $menuData->harga : '') }}" 
                                                   class="form-control form-control-sm @error('menus.'.$index.'.harga') is-invalid @enderror" 
                                                   placeholder="Rp" min="0">
                                            @error('menus.'.$index.'.harga')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="align-middle">
                                            <input type="number" name="menus[{{ $index }}][stok_awal]" 
                                                   value="{{ old('menus.'.$index.'.stok_awal', $menuData ? $menuData->stok_awal : '') }}" 
                                                   class="form-control form-control-sm @error('menus.'.$index.'.stok_awal') is-invalid @enderror" 
                                                   placeholder="Porsi" min="1">
                                            @error('menus.'.$index.'.stok_awal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge text-bg-secondary">{{ $menuData ? $menuData->stok_tersisa : '-' }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($menuData)
                                                <a href="{{ route('admin.menu-harian.extra.index', $menuData) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-solid fa-list-check"></i> Kelola Extra
                                                    <span class="badge text-bg-primary ms-1">{{ $menuData->extras->count() }}</span>
                                                </a>
                                            @else
                                                <small class="text-muted fst-italic">Simpan untuk kelola ekstra</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Semua</button>
                    <small class="text-muted ms-2 fst-italic">Hanya baris yang memiliki tanggal yang akan disimpan.</small>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
