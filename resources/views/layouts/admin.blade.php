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
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="bg-gradient-to-b from-gray-900 to-gray-800 text-gray-300 w-64 fixed inset-y-0 left-0 z-50 overflow-y-auto transition-transform duration-300 md:translate-x-0">
            <div class="p-4 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="text-lg font-bold text-white">Admin Panel</span>
                </a>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white md:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <nav class="mt-6 px-3 space-y-1">
                @php
                    $menuItems = [
                        ['route' => 'admin.dashboard', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>', 'label' => 'Dashboard'],
                        ['route' => 'admin.orders', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>', 'label' => 'Pesanan'],
                        ['route' => 'admin.catering.index', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>', 'label' => 'Katering'],
                        ['route' => 'admin.customers', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>', 'label' => 'Pelanggan'],
                        ['route' => 'admin.reviews', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>', 'label' => 'Ulasan'],
                        ['route' => 'admin.shipping.index', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>', 'label' => 'Ongkos Kirim'],
                        ['route' => 'admin.reports', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>', 'label' => 'Laporan'],
                    ];
                @endphp

                @foreach($menuItems as $item)
                    <a href="{{ route($item['route']) }}"
                       @click="sidebarOpen = false"
                       class="flex items-center px-3 py-2.5 rounded-lg text-sm transition-all
                              {{ request()->routeIs($item['route'] . '*') ? 'bg-orange-500/20 text-orange-400 font-medium' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="w-5 h-5 shrink-0 flex items-center justify-center">{!! $item['icon'] !!}</span>
                        <span class="ml-3">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Logout -->
            <div class="absolute bottom-0 w-full p-3">
                <form id="logout-form-admin" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="confirmLogout('logout-form-admin')" class="flex items-center w-full px-3 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-500/10 transition-all">
                        <span class="w-5 h-5 shrink-0 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg></span>
                        <span class="ml-3">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 transition-all duration-300 md:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <!-- Hamburger (mobile) -->
                        <button @click="sidebarOpen = true" class="text-gray-600 hover:text-gray-800 md:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-lg sm:text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500 hidden sm:inline">{{ auth()->user()->name }}</span>
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 sm:p-6">
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
                @if(session('warning'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({ icon: 'warning', title: 'Peringatan!', text: '{{ session("warning") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
                        });
                    </script>
                @endif
                @if(session('info'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({ icon: 'info', title: 'Informasi', text: '{{ session("info") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
                        });
                    </script>
                @endif
                @if($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({ icon: 'error', title: 'Periksa Data Input', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#f97316', confirmButtonText: 'Tutup' });
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
        // Konversi ke string dan hapus angka di belakang koma (titik desimal dari database) jika ada
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

    window.confirmDeleteForm = function(form, message) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: message || 'Data ini akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
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
