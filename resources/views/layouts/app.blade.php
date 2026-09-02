<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="deskripsi" content="Dapur Aisyah - Platform pemesanan katering online terpercaya di Pontianak">

    <title>{{ config('app.name', 'Dapur Aisyah') }} — @yield('title', 'Katering Online')</title>

    <!-- Fonts (Yummy Style) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">

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
            --primary-terracotta: #ce1212;
            --primary-hover: #a30e0e;
            --forest-green: #37373f;
            --forest-hover: #2b2b32;
            --bg-cream: #eeeeee;
            --text-dark: #212529;
            --soft-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 20px 40px rgba(206, 18, 18, 0.15);
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
        /* Yummy Navbar */
        .header {
            --background-color: #ffffff;
            --default-color: #212529;
            --heading-color: #37373f;
            --nav-color: #7f7f90;
            --nav-hover-color: #000;
            color: var(--default-color);
            background-color: var(--background-color);
            padding: 15px 0;
            transition: all 0.5s;
            z-index: 997;
            box-shadow: 0px 0 18px rgba(0, 0, 0, 0.1);
        }
        .header .logo h1 {
            font-size: 30px;
            margin: 0;
            font-weight: 700;
            color: var(--heading-color);
            font-family: 'Inter', sans-serif;
        }
        .header .logo h1 span {
            color: #ce1212;
        }
        .navmenu .nav-link {
            color: var(--nav-color);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            padding: 10px 15px !important;
            text-decoration: none;
            transition: 0.3s;
            position: relative;
        }
        .navmenu .nav-link:hover, .navmenu .nav-link.active {
            color: var(--nav-hover-color) !important;
            background-color: transparent !important;
        }
        .navmenu .nav-link:hover::before, .navmenu .nav-link.active::before {
            content: "";
            position: absolute;
            bottom: 2px;
            left: 15px;
            right: 15px;
            height: 2px;
            background: #ce1212;
        }
        .btn-outline-getstarted {
            color: #ce1212;
            background: transparent;
            border: 2px solid #ce1212;
            font-size: 14px;
            padding: 6px 25px;
            border-radius: 50px;
            transition: 0.3s;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-outline-getstarted:hover {
            color: #ffffff;
            background: #ce1212;
        }
        .btn-getstarted {
            color: #ffffff;
            background: #ce1212;
            border: 2px solid #ce1212;
            font-size: 14px;
            padding: 6px 25px;
            border-radius: 50px;
            transition: 0.3s;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-getstarted:hover {
            color: #ffffff;
            background: rgba(206, 18, 18, 0.9);
            border-color: rgba(206, 18, 18, 0.9);
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased text-secondary" {!! request()->routeIs('landing') ? 'data-bs-spy="scroll" data-bs-target="#navmenu" data-bs-offset="80"' : '' !!} style="background-color: #eeeeee;">
    
    <!-- Yummy Header -->
    <header id="header" class="header sticky-top d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('landing') }}#hero" class="logo d-flex align-items-center text-decoration-none">
                <h1 class="sitename">Dapur<span>Aisyah</span></h1>
            </a>

            <nav id="navmenu" class="navmenu navbar navbar-expand-lg flex-grow-1">
                <button class="navbar-toggler border-0 shadow-none d-lg-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('landing') && request()->hash == '' ? 'active' : '' }}" href="{{ request()->routeIs('landing') ? '#hero' : route('landing').'#hero' }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#services' : route('landing').'#services' }}">Layanan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#menu' : route('landing').'#menu' }}">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#gallery' : route('landing').'#gallery' }}">Galeri</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#testimonials' : route('landing').'#testimonials' }}">Testimoni</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ request()->routeIs('landing') ? '#about' : route('landing').'#about' }}">Tentang</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                        @auth
                            @if(Auth::user()->role === 'owner')
                                <a href="{{ route('owner.dashboard') }}" class="btn-getstarted">Dashboard Owner</a>
                            @elseif(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="btn-getstarted">Dashboard Admin</a>
                            @else
                                <a href="{{ route('pelanggan.riwayat') }}" class="btn-getstarted">Profil & Pesanan</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="btn text-danger fw-bold ms-2 border-0 bg-transparent">Keluar</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn-outline-getstarted">Masuk</a>
                            <a href="{{ route('register') }}" class="btn-getstarted">Daftar</a>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </header>

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
    <style>
        /* Yummy Footer Styles */
        .footer {
            font-size: 14px;
            background-color: #1f1f24;
            padding: 50px 0;
            color: rgba(255, 255, 255, 0.7);
            margin-top: auto;
        }
        .footer .icon {
            margin-right: 15px;
            font-size: 24px;
            line-height: 1;
            color: #ce1212;
        }
        .footer h4 {
            font-size: 16px;
            font-weight: bold;
            position: relative;
            padding-bottom: 5px;
            color: #fff;
            margin-bottom: 15px;
        }
        .footer .footer-links {
            margin-bottom: 30px;
        }
        .footer p {
            line-height: 1.5;
            margin-bottom: 0;
        }
        .footer .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            margin-right: 10px;
            transition: 0.3s;
            text-decoration: none;
        }
        .footer .social-links a:hover {
            color: #fff;
            border-color: #fff;
            background: #ce1212;
        }
        .footer .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
        }
        .footer .credits {
            padding-top: 4px;
            text-align: center;
            font-size: 13px;
        }
        .footer .credits a {
            color: #ce1212;
            text-decoration: none;
            font-weight: 500;
        }
    </style>

    <!-- Yummy Footer -->
    <footer id="footer" class="footer">
        <div class="container">
            <div class="row gy-3">
                <div class="col-lg-3 col-md-6 d-flex">
                    <i class="fa-solid fa-location-dot icon"></i>
                    <div>
                        <h4>Alamat Kami</h4>
                        <p>
                            Jl. Contoh Pontianak No. 123<br>
                            Pontianak, Kalimantan Barat<br>
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 footer-links d-flex">
                    <i class="fa-solid fa-phone icon"></i>
                    <div>
                        <h4>Pusat Bantuan</h4>
                        <p>
                            <strong>Telepon:</strong> 0812-3456-7890<br>
                            <strong>Email:</strong> info@dapuraisyah.com<br>
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 footer-links d-flex">
                    <i class="fa-regular fa-clock icon"></i>
                    <div>
                        <h4>Jam Operasional</h4>
                        <p>
                            <strong>Senin - Sabtu:</strong> 08.00 - 17.00<br>
                            <strong>Minggu:</strong> Tutup
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 footer-links">
                    <h4>Ikuti Kami</h4>
                    <div class="social-links d-flex">
                        <a href="#" class="facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="whatsapp"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Dapur Aisyah</span></strong>. All Rights Reserved
            </div>
            <div class="credits">
                Dirancang dengan <a href="#">Yummy Style</a>
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
