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
                    <a href="{{ route('landing') }}" class="flex items-center space-x-2">
                        <span class="text-2xl">🍲</span>
                        <span class="text-xl font-bold bg-gradient-to-r from-orange-500 to-amber-600 bg-clip-text text-transparent">Dapur Aisyah</span>
                    </a>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="hidden md:flex items-center flex-1 max-w-md mx-8">
                    <form action="{{ route('customer.products') }}" method="GET" class="w-full">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Cari menu favorit..." value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2 rounded-full border border-orange-200 focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition-all text-sm bg-orange-50/50">
                            <svg class="w-5 h-5 text-orange-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <!-- Notifications Bell -->
                        <a href="{{ route('customer.notifications') }}" class="relative p-2 text-gray-600 hover:text-orange-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @php $unreadCount = auth()->user()->unreadNotifications()->count() @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full animate-pulse">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Cart -->
                        <a href="{{ route('customer.cart') }}" class="relative p-2 text-gray-600 hover:text-orange-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                            </svg>
                            @php $cartCount = auth()->user()->carts()->count(); @endphp
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
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-orange-500 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-sm font-semibold rounded-full hover:shadow-lg hover:shadow-orange-200 transition-all transform hover:scale-105">
                            Daftar
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button x-data @click="$dispatch('toggle-mobile-menu')" class="p-2 text-gray-600 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        @auth
                            @php $hamburgerCartCount = auth()->user()->carts()->count(); @endphp
                            <span id="hamburger-cart-badge" class="absolute top-1 right-1 bg-orange-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full pointer-events-none {{ $hamburgerCartCount > 0 ? '' : 'hidden' }}">
                                {{ $hamburgerCartCount > 0 ? $hamburgerCartCount : '' }}
                            </span>
                        @endauth
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-show="open" x-transition class="md:hidden border-t border-orange-100 bg-white">
            <div class="px-4 py-3 space-y-2">
                <form action="{{ route('customer.products') }}" method="GET">
                    <input type="text" name="search" placeholder="Cari menu..." class="w-full px-4 py-2 rounded-lg border border-orange-200 text-sm">
                </form>
                @auth
                    <a href="{{ route('customer.profile.edit') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Profil Saya</a>
                    <a href="{{ route('customer.cart') }}" class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">
                        <span>Keranjang</span>
                        @php $mobileCartCount = auth()->user()->carts()->count(); @endphp
                        <span id="mobile-cart-badge" class="bg-orange-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full {{ $mobileCartCount > 0 ? '' : 'hidden' }}">
                            {{ $mobileCartCount > 0 ? $mobileCartCount : '' }}
                        </span>
                    </a>
                    <a href="{{ route('customer.orders') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Pesanan</a>
                    <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="button" onclick="confirmLogout('logout-form-mobile')" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 rounded-lg">Masuk</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-sm font-medium text-orange-600 hover:bg-orange-50 rounded-lg">Daftar</a>
                @endauth
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
                    confirmButtonColor: '#ef4444'
                });
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
                    <h3 class="text-lg font-bold text-white mb-4">🍲 Dapur Aisyah</h3>
                    <p class="text-sm text-gray-400">Platform pemesanan katering online terpercaya di Pontianak. Menyajikan menu berkualitas untuk kebutuhan harian dan acara kantor.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Menu</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('landing') }}" class="hover:text-orange-400 transition-colors">Beranda</a></li>
                        <li><a href="{{ route('customer.products') }}" class="hover:text-orange-400 transition-colors">Menu Kami</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-orange-400 transition-colors">Masuk</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center space-x-2">
                            <span>📍</span><span>Pontianak, Kalimantan Barat</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span>📞</span><span>0812-3456-7890</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span>✉️</span><span>info@dapuraisyah.com</span>
                        </li>
                    </ul>
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
