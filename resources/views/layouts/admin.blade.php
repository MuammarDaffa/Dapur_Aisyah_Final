<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Admin @yield('title')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside x-data="{ open: true }" :class="open ? 'w-64' : 'w-20'" class="bg-gradient-to-b from-gray-900 to-gray-800 text-gray-300 transition-all duration-300 fixed inset-y-0 left-0 z-40 overflow-y-auto">
            <div class="p-4 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2" x-show="open">
                    <span class="text-xl">🍲</span>
                    <span class="text-lg font-bold text-white">Admin Panel</span>
                </a>
                <button @click="open = !open" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <nav class="mt-6 px-3 space-y-1">
                @php
                    $menuItems = [
                        ['route' => 'admin.dashboard', 'icon' => '📊', 'label' => 'Dashboard'],
                        ['route' => 'admin.orders', 'icon' => '📦', 'label' => 'Pesanan'],
                        ['route' => 'admin.catering.index', 'icon' => '🍲', 'label' => 'Katering'],
                        ['route' => 'admin.customers', 'icon' => '👥', 'label' => 'Pelanggan'],
                        ['route' => 'admin.reviews', 'icon' => '⭐', 'label' => 'Ulasan'],
                        ['route' => 'admin.shipping.index', 'icon' => '🚚', 'label' => 'Ongkos Kirim'],
                        ['route' => 'admin.reports', 'icon' => '📈', 'label' => 'Laporan'],
                    ];
                @endphp

                @foreach($menuItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-sm transition-all
                              {{ request()->routeIs($item['route'] . '*') ? 'bg-orange-500/20 text-orange-400 font-medium' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="text-lg">{{ $item['icon'] }}</span>
                        <span class="ml-3" x-show="open">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Logout -->
            <div class="absolute bottom-0 w-full p-3">
                <form id="logout-form-admin" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="confirmLogout('logout-form-admin')" class="flex items-center w-full px-3 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-500/10 transition-all">
                        <span class="text-lg">🚪</span>
                        <span class="ml-3" x-show="open">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div x-data="{ sidebarOpen: true }" :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="flex-1 transition-all duration-300 ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session("success") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
                        });
                    </script>
                @endif
                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session("error") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
                        });
                    </script>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
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

    // Fungsi Global Format Rupiah
    function formatRupiah(angka) {
        if (!angka && angka !== 0) return '';
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
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
        // Inisialisasi input yang memiliki class rupiah-input
        const initRupiahInputs = () => {
            document.querySelectorAll('.rupiah-input').forEach(input => {
                // Jangan inisialisasi ulang jika sudah
                if (input.dataset.rupiahInit) return;
                input.dataset.rupiahInit = "true";

                // Format saat halaman dimuat
                if (input.value) {
                    input.value = formatRupiah(input.value);
                }

                // Format saat mengetik dan batasi maksimal 1 Miliar
                input.addEventListener('input', function(e) {
                    let rawValue = this.value.replace(/\./g, '');
                    if (parseInt(rawValue) > 1000000000) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Batas Maksimal Harga',
                            text: 'Harga tidak boleh lebih dari Rp 1.000.000.000.',
                            confirmButtonColor: '#f97316'
                        });
                        rawValue = '1000000000';
                    }
                    this.value = formatRupiah(rawValue);
                });

                // Hapus format sebelum submit form
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

        // Observer untuk menangkap elemen baru yang dimasukkan ke DOM (seperti modal Alpine.js / dinamis)
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
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    };
    </script>
    @stack('scripts')
</body>
</html>
