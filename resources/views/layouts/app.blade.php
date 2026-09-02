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
     <!-- responsive web link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- Dapur Aisyah: Modern Organic Gourmet Theme --- */
        :root {
            --primary-terracotta: #E05D36;
            --primary-hover: #C84B29;
            --forest-green: #2C4A3B;
            --forest-hover: #1E362A;
            --bg-cream: #F9F9F7;
            --text-dark: #1E293B;
            --soft-shadow: 0 12px 32px rgba(44, 74, 59, 0.08);
            --hover-shadow: 0 20px 40px rgba(224, 93, 54, 0.15);
            --bento-radius: 28px;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', 'Inter', 'Poppins', sans-serif;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-dark);
        }
        .text-primary-mc { color: var(--primary-terracotta) !important; }
        .text-accent-mc { color: var(--forest-green) !important; }

        /* Floating Navbar (Pill Design) */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 50px;
            margin: 20px auto;
            max-width: 95%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.4) !important;
            padding: 10px 20px !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @media (min-width: 992px) {
            .navbar-glass { max-width: 1200px; }
        }
        
        .navbar-nav .nav-link {
            color: var(--text-dark) !important;
            font-weight: 600;
            padding: 10px 20px !important;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            background-color: var(--forest-green) !important;
            color: white !important;
        }

        /* Premium Buttons */
        .btn-primary-mc {
            background-color: var(--primary-terracotta);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            padding: 12px 28px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            letter-spacing: 0.5px;
        }
        .btn-primary-mc:hover {
            background-color: var(--primary-hover);
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }
        .btn-accent-mc {
            background-color: var(--forest-green);
            color: white;
            border-radius: 50px;
            font-weight: 700;
            padding: 12px 28px;
            transition: all 0.3s ease;
        }
        .btn-accent-mc:hover {
            background-color: var(--forest-hover);
            color: white;
            transform: translateY(-3px);
        }

        /* Bento Cards */
        .card-premium {
            border: none;
            border-radius: var(--bento-radius);
            box-shadow: var(--soft-shadow);
            background: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }
        .card-premium:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Form Inputs */
        .form-control-mc {
            border-radius: 16px;
            border: 2px solid #E2E8F0;
            padding: 14px 20px;
            background-color: #F8FAFC;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .form-control-mc:focus {
            border-color: var(--forest-green);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(44, 74, 59, 0.1);
        }
        
        /* Badges */
        .badge-mc-success {
            background-color: var(--forest-green);
            color: white;
            border-radius: 20px;
            padding: 6px 14px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        /* Dropdown Animation */
        .nav-item-dropdown-custom .dropdown-menu {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 12px;
            margin-top: 15px;
            min-width: 220px;
        }
        .nav-item-dropdown-custom .dropdown-item {
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.2s;
            color: var(--text-dark);
        }
        .nav-item-dropdown-custom .dropdown-item:hover {
            background-color: #F1F5F9;
            color: var(--primary-terracotta);
            transform: translateX(4px);
        }

        /* Bento Footer */
        .footer-premium {
            background-color: var(--bg-cream);
            padding: 40px 0;
            color: var(--text-dark);
        }
        .bento-footer-box {
            background-color: white;
            border-radius: var(--bento-radius);
            padding: 35px;
            height: 100%;
            box-shadow: var(--soft-shadow);
            transition: all 0.3s ease;
        }
        .bento-footer-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        }
        .bento-footer-box.dark-box {
            background-color: var(--forest-green);
            color: white;
        }
        .bento-footer-box.dark-box h3, .bento-footer-box.dark-box h4 {
            color: white;
        }
        .bento-footer-box.accent-box {
            background-color: var(--primary-terracotta);
            color: white;
        }
        .footer-link {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        .footer-link:hover {
            color: var(--primary-terracotta);
            transform: translateX(5px);
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-light text-secondary" {!! request()->routeIs('landing') ? 'data-bs-spy="scroll" data-bs-target="#mainNavbar" data-bs-offset="80"' : '' !!}>
    <!-- Organic Floating Navbar -->
    <div class="fixed-top w-100 d-flex justify-content-center" style="z-index: 1030; pointer-events: none;">
        <nav class="navbar navbar-expand-lg navbar-light navbar-glass w-100" style="pointer-events: auto;">
            <div class="container-fluid px-3 px-lg-4">
                <!-- Logo -->
                <a class="navbar-brand fw-bold text-dark fs-4 d-flex align-items-center gap-3" href="{{ route('landing') }}#hero">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; background-color: var(--primary-terracotta);">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid p-2" style="filter: brightness(0) invert(1);">
                    </div>
                    <span style="letter-spacing: -1px;">Dapur<span class="text-primary-mc">Aisyah</span></span>
                </a>

                <!-- Hamburger -->
                <button class="navbar-toggler border-0 shadow-none bg-light rounded-circle p-2" style="width: 45px; height: 45px;" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Navbar Links -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 mt-3 mt-lg-0 text-center">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('landing') && request()->hash == '' ? 'active' : '' }}" href="{{ request()->routeIs('landing') ? '#hero' : route('landing').'#hero' }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#services' : route('landing').'#services' }}">Layanan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#about' : route('landing').'#about' }}">Filosofi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#testimonials' : route('landing').'#testimonials' }}">Cerita Pelanggan</a>
                        </li>
                    </ul>

                    <!-- Auth Links -->
                    <ul class="navbar-nav align-items-center gap-2 mt-3 mt-lg-0 justify-content-center">
                        @if(auth()->check() && (!auth()->user()->isCustomer() || auth()->user()->hasVerifiedEmail()))
                            <!-- Premium Dropdown -->
                            <li class="nav-item dropdown nav-item-dropdown-custom">
                                <a class="nav-link dropdown-toggle btn border-0 fw-bold text-dark d-flex align-items-center gap-3 p-1 pe-3 rounded-pill" style="background-color: #F1F5F9;" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; font-size: 0.9rem; background-color: var(--forest-green);">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    {{ explode(' ', trim(auth()->user()->name))[0] }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end mt-3 animate__animated animate__zoomIn animate__faster" aria-labelledby="userDropdown">
                                    <div class="px-3 py-2 mb-2 bg-light rounded-3 mx-2 text-center">
                                        <p class="mb-0 fw-bold text-dark">{{ auth()->user()->name }}</p>
                                        <p class="mb-0 small text-secondary">Pelanggan Setia</p>
                                    </div>
                                    <li><a class="dropdown-item d-flex align-items-center gap-3 {{ request()->routeIs('pelanggan.profile.edit') ? 'text-primary-mc' : '' }}" href="{{ route('pelanggan.profile.edit') }}"><div class="bg-light p-2 rounded-circle"><i class="fa-solid fa-user-circle"></i></div> Profil Saya</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-3 {{ request()->routeIs('pelanggan.riwayat') ? 'text-primary-mc' : '' }}" href="{{ route('pelanggan.riwayat') }}"><div class="bg-light p-2 rounded-circle"><i class="fa-solid fa-receipt"></i></div> Riwayat Pesanan</a></li>
                                    <li><hr class="dropdown-divider opacity-25"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" id="logout-form-desktop" class="m-0">
                                            @csrf
                                            <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-3" onclick="confirmLogout('logout-form-desktop')"><div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle"><i class="fa-solid fa-power-off"></i></div> Keluar</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @elseif(auth()->check() && auth()->user()->isCustomer() && !auth()->user()->hasVerifiedEmail())
                            <li class="nav-item">
                                <a href="{{ route('verification.notice') }}" class="nav-link text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Verifikasi Email</a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}" id="logout-form-desktop-unverified" class="m-0">
                                    @csrf
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" onclick="confirmLogout('logout-form-desktop-unverified')">Keluar</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link fw-bold text-dark"><i class="fa-solid fa-fingerprint me-1"></i> Masuk</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn-primary-mc text-decoration-none shadow-sm"><i class="fa-solid fa-leaf me-1"></i> Mulai Pesan</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
    </div>

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
    <!-- Bento Style Footer -->
    <footer class="footer-premium mt-auto pt-5">
        <div class="container py-4">
            <div class="row g-4">
                
                <!-- Bento Box 1: Brand -->
                <div class="col-lg-5 col-md-12">
                    <div class="bento-footer-box dark-box d-flex flex-column justify-content-between relative overflow-hidden" style="background: url('https://www.transparenttextures.com/patterns/cubes.png'), var(--forest-green);">
                        <div style="z-index: 1;">
                            <h3 class="fs-2 fw-bold mb-4 d-flex align-items-center gap-3">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 50px; height: 50px;">
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid p-2">
                                </div>
                                Dapur Aisyah
                            </h3>
                            <p class="mb-5 opacity-75 fs-5" style="line-height: 1.8; max-width: 90%;">
                                Menghadirkan simfoni citarasa organik rumahan untuk gaya hidup modern Anda di Pontianak.
                            </p>
                        </div>
                        <div class="d-flex gap-3" style="z-index: 1;">
                            <a href="#" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center fs-5 shadow-sm" style="width: 50px; height: 50px; color: var(--forest-green);"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center fs-5 shadow-sm" style="width: 50px; height: 50px; color: var(--forest-green);"><i class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Bento Box 2: Links -->
                <div class="col-lg-3 col-md-6">
                    <div class="bento-footer-box">
                        <h4 class="fs-5 text-dark mb-4 fw-bold">Eksplorasi</h4>
                        <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                            <li><a href="{{ request()->routeIs('landing') ? '#hero' : route('landing').'#hero' }}" class="footer-link"><i class="fa-solid fa-arrow-right-long me-2 text-primary-mc"></i> Beranda</a></li>
                            <li><a href="{{ request()->routeIs('landing') ? '#services' : route('landing').'#services' }}" class="footer-link"><i class="fa-solid fa-arrow-right-long me-2 text-primary-mc"></i> Pilihan Katering</a></li>
                            <li><a href="{{ request()->routeIs('landing') ? '#about' : route('landing').'#about' }}" class="footer-link"><i class="fa-solid fa-arrow-right-long me-2 text-primary-mc"></i> Filosofi Kami</a></li>
                            <li><a href="{{ request()->routeIs('landing') ? '#testimonials' : route('landing').'#testimonials' }}" class="footer-link"><i class="fa-solid fa-arrow-right-long me-2 text-primary-mc"></i> Cerita Pelanggan</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bento Box 3: Contact -->
                <div class="col-lg-4 col-md-6">
                    <div class="bento-footer-box accent-box mb-4">
                        <h4 class="fs-5 mb-3 fw-bold">Pusat Bantuan</h4>
                        <a href="#" class="text-white text-decoration-none fs-2 fw-bold d-block mb-1">0812-3456-7890</a>
                        <span class="opacity-75">Tersedia via WhatsApp</span>
                    </div>
                    <div class="bento-footer-box p-4" style="height: auto;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fa-solid fa-location-dot text-primary-mc fs-5"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold text-dark">Dapur Utama</span>
                                <span class="text-secondary small">Jl. Contoh Pontianak No. 123</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright Bottom -->
        <div class="container pb-4 pt-3">
            <div class="bg-white rounded-pill px-4 py-3 shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="mb-0 text-secondary small fw-bold">&copy; {{ date('Y') }} Dapur Aisyah.</p>
                <div class="small fw-bold" style="color: var(--primary-terracotta);">
                    Dirancang dengan ❤️ & 🍃
                </div>
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

    window.confirmDeleteForm = function(form, message) {
        Swal.fire({
            title: 'Hapus Pesanan?',
            text: message || "Apakah Anda yakin ingin menghapus pesanan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    };

    window.confirmCancelForm = function(form, message) {
        Swal.fire({
            title: 'Batalkan Pesanan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Kembali',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
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
