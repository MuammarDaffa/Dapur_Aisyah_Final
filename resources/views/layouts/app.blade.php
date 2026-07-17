<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dapur Aisyah - Platform pemesanan katering online terpercaya di Pontianak">

    <title>{{ config('app.name', 'Dapur Aisyah') }} — @yield('title', 'Katering Online')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-lg border-b border-orange-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    @if(request()->routeIs('landing'))
                        <a href="#hero" class="logo-nav-link flex items-center space-x-2.5 sm:space-x-3" data-target="hero">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Dapur Aisyah" class="h-8 sm:h-9 md:h-10 w-auto object-contain">
                            <span class="text-xl font-bold bg-gradient-to-r from-orange-500 to-amber-600 bg-clip-text text-transparent">Dapur Aisyah</span>
                        </a>
                    @else
                        <a href="{{ route('landing') }}#hero" class="flex items-center space-x-2.5 sm:space-x-3">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Dapur Aisyah" class="h-8 sm:h-9 md:h-10 w-auto object-contain">
                            <span class="text-xl font-bold bg-gradient-to-r from-orange-500 to-amber-600 bg-clip-text text-transparent">Dapur Aisyah</span>
                        </a>
                    @endif
                </div>

                <!-- Navbar Links (Replace Search Bar on Desktop) -->
                <div class="hidden md:flex items-center justify-center flex-1 space-x-10 mx-8">
                    @if(request()->routeIs('landing'))
                        <a href="#hero" class="main-nav-link text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent" data-target="hero">
                            Beranda
                        </a>
                        <a href="#services" class="main-nav-link text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent" data-target="services">
                            Layanan
                        </a>
                        <a href="#about" class="main-nav-link text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent" data-target="about">
                            Tentang
                        </a>
                        @if(isset($reviews) && $reviews->count() > 0)
                        <a href="#testimonials" class="main-nav-link text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent" data-target="testimonials">
                            Testimoni
                        </a>
                        @endif
                    @else
                        <a href="{{ route('landing') }}#hero" class="text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent">
                            Beranda
                        </a>
                        <a href="{{ route('landing') }}#services" class="text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent">
                            Layanan
                        </a>
                        <a href="{{ route('landing') }}#about" class="text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent">
                            Tentang
                        </a>
                        <a href="{{ route('landing') }}#testimonials" class="text-sm font-semibold text-gray-600 hover:text-orange-500 transition-all duration-300 py-1.5 border-b-2 border-transparent">
                            Testimoni
                        </a>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-4">
                    @if(auth()->check() && (!auth()->user()->isCustomer() || auth()->user()->hasVerifiedEmail()))
                        <!-- Cart -->
                        <a href="{{ route('customer.cart') }}" class="relative p-2 text-gray-600 hover:text-orange-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                            </svg>
                            @php $cartCount = auth()->user()->cartItemsCount(); @endphp
                            <span id="desktop-cart-badge" class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full {{ $cartCount > 0 ? '' : 'hidden' }}">
                                {{ $cartCount > 0 ? $cartCount : '' }}
                            </span>
                        </a>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-orange-500 transition-colors">
                                <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden lg:inline">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Profil Saya</a>
                                <a href="{{ route('customer.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Pesanan Saya</a>
                                <hr class="my-1 border-gray-100">
                                <form id="logout-form-desktop" method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="button" onclick="confirmLogout('logout-form-desktop')" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                                </form>
                            </div>
                        </div>
                    @elseif(auth()->check() && auth()->user()->isCustomer() && !auth()->user()->hasVerifiedEmail())
                        <a href="{{ route('verification.notice') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">Verifikasi Email</a>
                        <form id="logout-form-desktop-unverified" method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="button" onclick="confirmLogout('logout-form-desktop-unverified')" class="text-sm font-medium text-gray-700 hover:text-red-600 transition-colors">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-orange-500 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-sm font-semibold rounded-full hover:shadow-lg hover:shadow-orange-200 transition-all transform hover:scale-105">
                            Daftar
                        </a>
                    @endif
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button x-data @click="$dispatch('toggle-mobile-menu')" class="p-2 text-gray-600 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        @if(auth()->check() && (!auth()->user()->isCustomer() || auth()->user()->hasVerifiedEmail()))
                            @php $hamburgerCartCount = auth()->user()->cartItemsCount(); @endphp
                            <span id="hamburger-cart-badge" class="absolute top-1 right-1 bg-orange-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full pointer-events-none {{ $hamburgerCartCount > 0 ? '' : 'hidden' }}">
                                {{ $hamburgerCartCount > 0 ? $hamburgerCartCount : '' }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-show="open" x-transition class="md:hidden border-t border-orange-100 bg-white">
            <div class="px-4 py-3 space-y-2">
                @if(auth()->check() && (!auth()->user()->isCustomer() || auth()->user()->hasVerifiedEmail()))
                    @if(request()->routeIs('landing'))
                        <a href="#hero" @click="open = false" class="main-nav-link block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg" data-target="hero">Beranda</a>
                    @else
                        <a href="{{ route('landing') }}#hero" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Beranda</a>
                    @endif
                    <a href="{{ route('customer.profile.edit') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Profil Saya</a>
                    <a href="{{ route('customer.cart') }}" class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">
                        <span>Keranjang</span>
                        @php $mobileCartCount = auth()->user()->cartItemsCount(); @endphp
                        <span id="mobile-cart-badge" class="bg-orange-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full {{ $mobileCartCount > 0 ? '' : 'hidden' }}">
                            {{ $mobileCartCount > 0 ? $mobileCartCount : '' }}
                        </span>
                    </a>
                    <a href="{{ route('customer.orders') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Pesanan</a>
                    <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="button" onclick="confirmLogout('logout-form-mobile')" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Keluar</button>
                    </form>
                @elseif(auth()->check() && auth()->user()->isCustomer() && !auth()->user()->hasVerifiedEmail())
                    <a href="{{ route('verification.notice') }}" class="block px-3 py-2 text-sm font-semibold text-orange-600 hover:bg-orange-50 rounded-lg">Verifikasi Email</a>
                    <form id="logout-form-mobile-unverified" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="button" onclick="confirmLogout('logout-form-mobile-unverified')" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Keluar</button>
                    </form>
                @else
                    @if(request()->routeIs('landing'))
                        <a href="#hero" @click="open = false" class="main-nav-link block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg" data-target="hero">Beranda</a>
                    @else
                        <a href="{{ route('landing') }}#hero" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Beranda</a>
                    @endif
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Masuk</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-sm font-medium text-orange-600 hover:bg-orange-50 rounded-lg">Daftar</a>
                @endif
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
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Dapur Aisyah</h3>
                    <p class="text-sm text-gray-400">Platform pemesanan katering online terpercaya di Pontianak. Menyajikan menu berkualitas untuk kebutuhan harian dan acara kantor.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Menu</h4>
                    <ul class="space-y-2 text-sm">
                        @if(request()->routeIs('landing'))
                            <li><a href="#hero" class="footer-nav-link hover:text-orange-400 transition-colors" data-target="hero">Beranda</a></li>
                            <li><a href="#services" class="footer-nav-link hover:text-orange-400 transition-colors" data-target="services">Layanan</a></li>
                            <li><a href="#about" class="footer-nav-link hover:text-orange-400 transition-colors" data-target="about">Tentang</a></li>
                            @if(isset($reviews) && $reviews->count() > 0)
                            <li><a href="#testimonials" class="footer-nav-link hover:text-orange-400 transition-colors" data-target="testimonials">Testimoni</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('landing') }}#hero" class="hover:text-orange-400 transition-colors">Beranda</a></li>
                            <li><a href="{{ route('landing') }}#services" class="hover:text-orange-400 transition-colors">Layanan</a></li>
                            <li><a href="{{ route('landing') }}#about" class="hover:text-orange-400 transition-colors">Tentang</a></li>
                            <li><a href="{{ route('landing') }}#testimonials" class="hover:text-orange-400 transition-colors">Testimoni</a></li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm mb-4">
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>0812-3456-7890</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>info@dapuraisyah.com</span>
                        </li>
                    </ul>
                    <div class="w-full rounded-xl overflow-hidden border border-gray-700 shadow-sm h-36 sm:h-40 md:h-36">
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
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Dapur Aisyah. Semua hak dilindungi.</p>
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

    window.updateCartBadges = function(count) {
        const desktopBadge = document.getElementById('desktop-cart-badge');
        const mobileBadge = document.getElementById('mobile-cart-badge');
        const hamburgerBadge = document.getElementById('hamburger-cart-badge');
        [desktopBadge, mobileBadge, hamburgerBadge].forEach(badge => {
            if (!badge) return;
            const num = parseInt(count);
            if (!isNaN(num) && num > 0) {
                badge.textContent = num;
                badge.classList.remove('hidden');
            } else {
                badge.textContent = '';
                badge.classList.add('hidden');
            }
        });
    };

    window.refreshCartBadges = function() {
        fetch('{{ route("customer.cart.count") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && typeof data.cart_count !== 'undefined') {
                window.updateCartBadges(data.cart_count);
            }
        })
        .catch(() => {});
    };

    window.submitQuickAddCart = function(e, form) {
        if (e && e.preventDefault) e.preventDefault();
        if (!form) return false;
        const btn = form.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (btn) btn.disabled = false;
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
                return;
            }
            if (data.success) {
                if (typeof window.updateCartBadges === 'function' && typeof data.cart_count !== 'undefined') {
                    window.updateCartBadges(data.cart_count);
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Produk berhasil ditambahkan ke keranjang!',
                        showConfirmButton: true,
                        confirmButtonText: 'Oke',
                        confirmButtonColor: '#f97316'
                    });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat menambahkan ke keranjang.',
                        confirmButtonColor: '#f97316'
                    });
                } else {
                    form.submit();
                }
            }
        })
        .catch(err => {
            if (btn) btn.disabled = false;
            form.submit();
        });
        return false;
    };
    </script>
    @stack('scripts')
</body>
</html>
