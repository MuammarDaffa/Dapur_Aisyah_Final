import re

filepath = r"E:\TA\Dapur_Aisyah\resources\views\layouts\app.blade.php"
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace everything from <nav ...> to </nav>
navbar_bootstrap = """<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
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
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ request()->routeIs('landing') ? '#hero' : route('landing').'#hero' }}">Beranda</a>
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
                    <!-- Cart -->
                    <li class="nav-item me-3">
                        <a href="{{ route('customer.cart') }}" class="nav-link position-relative text-dark">
                            Keranjang
                            @php $cartCount = auth()->user()->cartItemsCount(); @endphp
                            @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $cartCount }}
                            </span>
                            @endif
                        </a>
                    </li>
                    <!-- Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('customer.profile.edit') }}">Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('customer.orders') }}">Pesanan Saya</a></li>
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
</nav>"""

# Using regex to replace the old <nav>...</nav>
new_content = re.sub(r'<nav.*?</nav>', navbar_bootstrap, content, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)
    
print("Updated app.blade.php navbar!")
