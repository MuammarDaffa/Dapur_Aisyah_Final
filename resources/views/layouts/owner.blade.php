<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Owner @yield('title')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-light">
    <div class="min-vh-100 d-flex">
        <!-- Sidebar -->
        <aside x-data="{ open: true }" :class="open ? 'w-64' : 'w-20'" class="text-secondary position-fixed inset-y-0 overflow-y-auto">
            <div class="p-4 d-flex align-items-center justify-content-between">
                <a href="{{ route('owner.dashboard') }}" class="d-flex align-items-center space-x-2" x-show="open">
                    <span class="fs-4">🍲</span>
                    <span class="fs-5 fw-bold text-white">Owner Panel</span>
                </a>
                <button @click="open = !open" class="text-secondary hover:text-white">
                    <svg style="width: 20px; height: 20px;" class="" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            {{-- Read-Only Badge --}}
            <div class="px-4 mb-4" x-show="open">
                <div class="bg-teal-700/50 text-teal-200 small fw-medium px-3 py-1.5 rounded text-center">
                    🔒 Mode Read-Only
                </div>
            </div>

            <nav class="mt-2 px-3 space-y-1">
                @php
                    $menuItems = [
                        ['route' => 'owner.dashboard', 'icon' => '📊', 'label' => 'Dashboard'],
                        ['route' => 'owner.pesanan', 'icon' => '🛒', 'label' => 'Pesanan'],
                        ['route' => 'owner.customers', 'icon' => '👥', 'label' => 'Pelanggan'],
                        ['route' => 'owner.ulasan', 'icon' => '💬', 'label' => 'Ulasan'],
                        ['route' => 'owner.reports', 'icon' => '📈', 'label' => 'Laporan'],
                    ];
                @endphp

                @foreach($menuItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="d-flex align-items-center px-3 py-2.5 rounded fs-6 {{ request()->routeIs($item['route'] . '*') ? 'bg-teal-500/20 text-teal-300 fw-medium' : 'hover:bg-white/5 hover:text-white' }}">
                        <span class="fs-5">{{ $item['icon'] }}</span>
                        <span class="ms-3" x-show="open">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Logout -->
            <div class="position-absolute w-100 p-3">
                <form id="logout-form-owner" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="confirmLogout('logout-form-owner')" class="btn btn-outline-danger btn btn-danger d-flex align-items-center w-100 px-3 py-2.5 rounded fs-6 text-danger hover:bg-danger text-white/10">
                        <span class="fs-5">🚪</span>
                        <span class="ms-3" x-show="open">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div x-data="{ sidebarOpen: true }" :class="sidebarOpen ? 'ms-64' : 'ms-20'" class="d-flex-1 ms-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border border-secondary sticky-top">
                <div class="px-6 py-4 d-flex justify-content-between align-items-center">
                    <h1 class="fs-4 fw-bold text-secondary">@yield('title', 'Dashboard Owner')</h1>
                    <div class="d-flex align-items-center space-x-3">
                        <span class="fs-6 text-secondary">{{ auth()->user()->name }}</span>
                        <div style="width: 32px; height: 32px;" class="rounded-pill d-flex align-items-center justify-content-center text-white fs-6 fw-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <x-toast />
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
    </script>
    @stack('scripts')
</body>
</html>
