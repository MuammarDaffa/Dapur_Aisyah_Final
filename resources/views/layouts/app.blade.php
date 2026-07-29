<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="deskripsi" content="Dapur Aisyah - Platform pemesanan katering online terpercaya di Pontianak">

    <title>{{ config('app.name', 'Dapur Aisyah') }} — @yield('title', 'Katering Online')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-light text-secondary" {!! request()->routeIs('landing') ? 'data-bs-spy="scroll" data-bs-target="#mainNavbar" data-bs-offset="80"' : '' !!}>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold text-primary" href="{{ route('landing') }}#hero">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Dapur Aisyah" height="30" class="d-inline-block align-text-top me-2">
            Dapur Aisyah
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('landing') && request()->hash == '' ? 'active' : '' }}" href="{{ request()->routeIs('landing') ? '#hero' : route('landing').'#hero' }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ request()->routeIs('landing') ? '#services' : route('landing').'#services' }}">Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ request()->routeIs('landing') ? '#about' : route('landing').'#about' }}">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ request()->routeIs('landing') ? '#testimonials' : route('landing').'#testimonials' }}">Testimoni</a>
                </li>
            </ul>

            <!-- Auth Links -->
            <ul class="navbar-nav">
                @if(auth()->check() && (!auth()->user()->isCustomer() || auth()->user()->hasVerifiedEmail()))

                    <!-- Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('pelanggan.profile.edit') ? 'active fw-bold' : '' }}" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('pelanggan.profile.edit') ? 'active' : '' }}" href="{{ route('pelanggan.profile.edit') }}">Profil Saya</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('pelanggan.riwayat') ? 'active' : '' }}" href="{{ route('pelanggan.riwayat') }}">Riwayat Pesanan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form-desktop" class="m-0">
                                    @csrf
                                    <button type="button" class="dropdown-item text-danger" onclick="confirmLogout('logout-form-desktop')">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @elseif(auth()->check() && auth()->user()->isCustomer() && !auth()->user()->hasVerifiedEmail())
                    <li class="nav-item">
                        <a href="{{ route('verification.notice') }}" class="nav-link text-warning">Verifikasi Email</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-desktop-unverified" class="m-0">
                            @csrf
                            <button type="button" class="btn btn-link nav-link text-danger" onclick="confirmLogout('logout-form-desktop-unverified')">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-3">Daftar</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

    <!-- Flash Messages / Toast -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil!', 
                    text: '{{ session("success") }}', 
                    showConfirmButton: true, 
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#f97316'
                });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal!', 
                    text: '{{ session("error") }}', 
                    showConfirmButton: true, 
                    confirmButtonText: 'Oke',
                });
            });
        </script>
    @endif
    @if(session('event_conflict_error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Perhatian', 
                    text: '{!! session("event_conflict_error") !!}', 
                    showConfirmButton: true, 
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#f97316'
                });
            });
        </script>
    @endif
    @if(session('info'))
        @php $infoMsg = session('info'); session()->forget('info'); @endphp
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const notifText = {!! json_encode($infoMsg) !!};
                const storageKey = 'swal_notif_info_' + btoa(unescape(encodeURIComponent(notifText)));
                if (!sessionStorage.getItem(storageKey)) {
                    sessionStorage.setItem(storageKey, 'true');
                    Swal.fire({ 
                        icon: 'info', 
                        title: 'Informasi', 
                        text: notifText, 
                        showConfirmButton: true, 
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#f97316',
                        didClose: () => {
                            sessionStorage.removeItem(storageKey);
                            fetch('{{ route("session.clear-notification") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ keys: ['info', 'warning'] })
                            }).catch(() => {});
                        }
                    });
                }
            });
        </script>
    @endif
    @if(session('warning'))
        @php $warningMsg = session('warning'); session()->forget('warning'); @endphp
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const notifText = {!! json_encode($warningMsg) !!};
                const storageKey = 'swal_notif_warning_' + btoa(unescape(encodeURIComponent(notifText)));
                if (!sessionStorage.getItem(storageKey)) {
                    sessionStorage.setItem(storageKey, 'true');
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Perhatian!', 
                        text: notifText, 
                        showConfirmButton: true, 
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#f97316',
                        didClose: () => {
                            sessionStorage.removeItem(storageKey);
                            fetch('{{ route("session.clear-notification") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ keys: ['info', 'warning'] })
                            }).catch(() => {});
                        }
                    });
                }
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    showConfirmButton: true,
                    confirmButtonText: 'Perbaiki',
                    confirmButtonColor: '#ef4444'
                });
            });
        </script>
    @endif

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>
    <!-- Footer -->
    <footer class="bg-dark text-light mt-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <h3 class="fs-5 fw-bold text-white mb-3">Dapur Aisyah</h3>
                    <p class="text-light">Platform pemesanan katering online terpercaya di Pontianak. Menyajikan menu berkualitas untuk kebutuhan harian dan acara kantor.</p>
                </div>
                <div class="col-md-4">
                    <h4 class="fs-6 fw-bold text-white text-uppercase mb-3">Menu</h4>
                    <ul class="list-unstyled">
                        @if(request()->routeIs('landing'))
                            <li class="mb-2"><a href="#hero" class="text-light text-decoration-none" data-target="hero">Beranda</a></li>
                            <li class="mb-2"><a href="#services" class="text-light text-decoration-none" data-target="services">Layanan</a></li>
                            <li class="mb-2"><a href="#about" class="text-light text-decoration-none" data-target="about">Tentang</a></li>
                            @if(isset($ulasan) && $ulasan->count() > 0)
                            <li class="mb-2"><a href="#testimonials" class="text-light text-decoration-none" data-target="testimonials">Testimoni</a></li>
                            @endif
                        @else
                            <li class="mb-2"><a href="{{ route('landing') }}#hero" class="text-light text-decoration-none">Beranda</a></li>
                            <li class="mb-2"><a href="{{ route('landing') }}#services" class="text-light text-decoration-none">Layanan</a></li>
                            <li class="mb-2"><a href="{{ route('landing') }}#about" class="text-light text-decoration-none">Tentang</a></li>
                            <li class="mb-2"><a href="{{ route('landing') }}#testimonials" class="text-light text-decoration-none">Testimoni</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="fs-6 fw-bold text-white text-uppercase mb-3">Kontak</h4>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2 d-flex align-items-center">
                            <svg style="width: 16px; height: 16px;" class="text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>0812-3456-7890</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <svg style="width: 16px; height: 16px;" class="text-primary me-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>info@dapuraisyah.com</span>
                        </li>
                    </ul>
                    <div class="w-100 rounded overflow-hidden border border-secondary shadow-sm" style="height: 150px;">
                        <iframe 
                            src="https://maps.google.com/maps?q=-0.060394,109.301565&z=15&output=embed" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi Dapur Aisyah">
                        </iframe>
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary mt-5 pt-4 text-center text-light">
                <p class="mb-0">&copy; {{ date('Y') }} Dapur Aisyah. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
    function confirmLogout(formId) {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }


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
