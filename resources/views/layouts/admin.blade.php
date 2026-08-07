<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Admin @yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Main Header -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="fa-solid fa-bars"></i></a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <!-- User Menu -->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-md-inline">Halo, {{ auth()->user()->name ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <p>
                                    {{ auth()->user()->name ?? 'Admin' }}
                                    <small>{{ auth()->user()->email ?? '' }}</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <form id="logout-form-admin" method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="button" onclick="confirmLogout('logout-form-admin')" class="btn btn-danger btn-flat float-end">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Main Sidebar -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('admin.dashboard') }}" class="brand-link">
                    <span class="brand-text fw-bold">Dapur Aisyah</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        @php
                            $menuItems = [
                                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-solid fa-house'],
                                ['route' => 'admin.pesanan', 'label' => 'Pesanan', 'icon' => 'fa-solid fa-shopping-cart'],
                                ['route' => 'admin.catering.harian', 'label' => 'Katering Harian', 'icon' => 'fa-solid fa-calendar-day'],
                                ['route' => 'admin.catering.acara', 'label' => 'Katering Acara Kantoran', 'icon' => 'fa-solid fa-glass-cheers'],
                                ['route' => 'admin.customers', 'label' => 'Pelanggan', 'icon' => 'fa-solid fa-users'],
                            ];
                        @endphp
                        @foreach($menuItems as $item)
                            <li class="nav-item">
                                <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['route'] . '*') ? 'active' : '' }}">
                                    <i class="nav-icon {{ $item['icon'] }}"></i>
                                    <p>{{ $item['label'] }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>
        
        <!-- Content Wrapper -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">@yield('title', 'Dashboard')</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="app-content">
                <div class="container-fluid">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Content -->
                    @yield('content')
                </div>
            </div>
        </main>
        
        <footer class="app-footer">
            <strong>Copyright &copy; {{ date('Y') }} Dapur Aisyah.</strong>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
    <script>
    function confirmLogout(formId) {
        Swal.fire({
            title: 'Keluar?',
            text: "Apakah Anda yakin ingin logout?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        })
    }
    window.confirmDelete = function(formId, message) {
        Swal.fire({
            title: 'Hapus Data?',
            text: message || "Data ini tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        })
    };
    window.confirmDeleteForm = function(form, message) {
        Swal.fire({
            title: 'Hapus Data?',
            text: message || "Data ini tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    };
    </script>
    <script>
// Fungsi Global Format Rupiah
    function formatRupiah(angka) {
        if (!angka && angka !== 0) return '';
        let string_angka = angka.toString();
        if(!isNaN(string_angka) && string_angka.includes('.')) {
            string_angka = Math.round(parseFloat(string_angka)).toString();
        }
        let number_string = string_angka.replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const initRupiahInputs = () => {
            document.querySelectorAll('.rupiah-input').forEach(input => {
                if (input.dataset.rupiahInit) return;
                input.dataset.rupiahInit = "true";
                if (input.value) {
                    input.value = formatRupiah(input.value);
                }
                input.addEventListener('input', function(e) {
                    let rawValue = this.value.replace(/\./g, '');
                    if (parseInt(rawValue) > 1000000000) {
                        alert('Harga tidak boleh lebih dari Rp 1.000.000.000.');
                        rawValue = '1000000000';
                    }
                    this.value = formatRupiah(rawValue);
                });
                const form = input.closest('form');
                if (form && !form.dataset.rupiahFormInit) {
                    form.dataset.rupiahFormInit = "true";
                    form.addEventListener('submit', () => {
                        form.querySelectorAll('.rupiah-input').forEach(inp => {
                            inp.value = inp.value.replace(/\./g, '');
                        });
                    });
                }
            });
        };
        initRupiahInputs();
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    initRupiahInputs();
                }
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });

 
    </script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    @stack('scripts')
</body>
</html>
