<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Admin @yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @stack('styles')
</head>
<body class="bg-light font-sans antialiased">
    
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Dapur Aisyah Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="adminNavbar">
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item me-3">
                        <span class="nav-link text-white">Halo, {{ auth()->user()->name ?? 'Admin' }}</span>
                    </li>
                    <li class="nav-item">
                        <form id="logout-form-admin" method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="button" onclick="confirmLogout('logout-form-admin')" class="btn btn-danger">Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-white sidebar border-end min-vh-100 py-3">
                <div class="position-sticky">
                    <ul class="nav flex-column gap-1 px-2">
                        @php
                            $menuItems = [
                                ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                                ['route' => 'admin.orders', 'label' => 'Pesanan'],
                                ['route' => 'admin.catering.index', 'label' => 'Katering'],
                                ['route' => 'admin.customers', 'label' => 'Pelanggan'],
                                ['route' => 'admin.reviews', 'label' => 'Ulasan'],
                                ['route' => 'admin.shipping.index', 'label' => 'Ongkos Kirim'],
                                ['route' => 'admin.reports', 'label' => 'Laporan'],
                            ];
                        @endphp

                        @foreach($menuItems as $item)
                            <li class="nav-item">
                                <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['route'] . '*') ? 'active bg-primary text-white rounded' : 'text-dark hover:bg-light rounded' }}">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title', 'Dashboard')</h1>
                </div>

                <!-- Session Alerts -->
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

                <!-- Content Slot -->
                @yield('content')
                
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function confirmLogout(formId) {
        if(confirm('Apakah Anda yakin ingin logout?')) {
            document.getElementById(formId).submit();
        }
    }

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

    window.confirmDelete = function(formId, message) {
        if(confirm(message || 'Yakin hapus?')) {
            document.getElementById(formId).submit();
        }
    };
    window.confirmDeleteForm = function(form, message) {
        if(confirm(message || 'Data ini akan dihapus secara permanen.')) {
            form.submit();
        }
    };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
    window.initIndonesianDatePickers = function() {
        if (typeof flatpickr !== 'undefined' && flatpickr.l10n && flatpickr.l10n.id) {
            flatpickr.localize(flatpickr.l10n.id);
            document.querySelectorAll('input[type="date"]').forEach(function(input) {
                if (input._flatpickr) return;
                flatpickr(input, {
                    locale: "id",
                    dateFormat: "Y-m-d",
                    disableMobile: true,
                    minDate: input.getAttribute('min') || undefined,
                    maxDate: input.getAttribute('max') || undefined,
                    onChange: function(selectedDates, dateStr, instance) {
                        instance.element.dispatchEvent(new Event('change', { bubbles: true }));
                        instance.element.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            });
        }
    };
    document.addEventListener('DOMContentLoaded', window.initIndonesianDatePickers);
    </script>
    @stack('scripts')
</body>
</html>
