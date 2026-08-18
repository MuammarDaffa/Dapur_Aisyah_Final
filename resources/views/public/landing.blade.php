@extends('layouts.app')

@section('title', 'Katering Online Terpercaya Pontianak')

@section('content')
    <style>
       
        .hero-section {
            height: 100vh;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-bg {
            transition: opacity 800ms ease-in-out;
        }
        .hero-btn {
            background-color: #f97316; /* primary orange color */
            color: #ffffff;
            border: 2px solid #f97316;
            transition: all 250ms ease-in-out;
        }
        .hero-btn:hover {
            background-color: transparent;
            color: #f97316;
            border-color: #f97316;
        }
        .hero-btn:active {
            transform: translateY(1px);
        }
        @media (min-width: 640px) and (max-width: 1023px) {
            .hero-section {
                height: 100vh;
                min-height: 100vh;
                padding: 6rem 1rem;
            }
        }
        @media (min-width: 1024px) {
            .hero-section {
                height: 100vh;
                min-height: 100vh;
            }
        }
        .section-item {
            min-height: calc(100vh - 64px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-top: 5rem;
            padding-bottom: 5rem;
            scroll-margin-top: 64px;
        }
    </style>

    <!-- Hero Section -->
    <section id="hero" class="position-relative overflow-hidden d-flex align-items-center justify-content-center text-center hero-section w-100 section-item" style="background-color: #333;">
        <!-- Background Container -->
        <div class="position-absolute top-0 start-0 w-100 h-100">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('{{ asset('images/katering.png') }}'); background-size: cover; background-position: center;"></div>
            <!-- Dark Overlay -->
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.6;"></div>
        </div>

        <!-- Content Container -->
            <div class="position-relative container d-flex flex-column align-items-center justify-content-center h-100 z-1" style="z-index: 2;">
                <h1 class="display-2 fw-bold text-warning mb-4" style="max-width: 800px;">
                    Dapur Aisyah 
                </h1>
                <h4 class="display-8 fw-bold text-white mb-5" style="max-width: 700px;">
                    Katering Rumahan Berkualitas Premium
                </h4>
                <div>
                    <a href="#services" class="btn btn-warning text-white  btn-lg rounded-0 px-5 py-3 fw-bold shadow scroll-to-services mb-5">
                        Pesan Sekarang
                    </a>
                    
                </div>
                <marquee class="fs-5 text-light mb-5 text-2xl" style="max-width: 700px;">
                    Nikmati masakan rumahan berkualitas untuk kebutuhan harian atau acara.
                </marquee>
            </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollBtn = document.querySelector('.scroll-to-services');
            if (scrollBtn) {
                scrollBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.getElementById('services');
                    if (target) {
                        const navOffset = 64;
                        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navOffset;
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            }

            // Main Navbar Smooth Scroll & Active Status (Scroll Spy)
            const mainNavLinks = document.querySelectorAll('.main-nav-link');
            const scrollLinks = document.querySelectorAll('.main-nav-link, .logo-nav-link, .footer-nav-link');
            const sections = document.querySelectorAll('section.section-item[id]');

            // Smooth Scroll on Link Click
            scrollLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');
                    const targetEl = document.getElementById(targetId);
                    if (targetEl) {
                        const navOffset = 64;
                        const targetPosition = targetEl.getBoundingClientRect().top + window.pageYOffset - navOffset;
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Active Navigation on Scroll (Scroll Spy)
            function updateActiveNavigation() {
                if (window.innerWidth < 768 || mainNavLinks.length === 0) return;

                const scrollPos = window.pageYOffset + 80;
                let currentSectionId = 'hero';

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                        currentSectionId = section.getAttribute('id');
                    }
                });

                // If scrolled near bottom of page, activate the last visible section ('testimonials' if present, otherwise 'about')
                if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight - 50) {
                    const testimonialsSection = document.getElementById('testimonials');
                    const aboutSection = document.getElementById('about');
                    if (testimonialsSection) {
                        currentSectionId = 'testimonials';
                    } else if (aboutSection) {
                        currentSectionId = 'about';
                    }
                }

                mainNavLinks.forEach(link => {
                    const targetId = link.getAttribute('data-target');
                    if (targetId === currentSectionId) {
                        link.classList.remove('text-gray-600', 'border-transparent');
                        link.classList.add('text-orange-600', 'border-orange-500');
                    } else {
                        link.classList.remove('text-orange-600', 'border-orange-500');
                        link.classList.add('text-gray-600', 'border-transparent');
                    }
                });
            }

            window.addEventListener('scroll', updateActiveNavigation, { passive: true });
            window.addEventListener('resize', updateActiveNavigation, { passive: true });
            updateActiveNavigation(); // Initial check
        });
    </script>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-white section-item">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="fs-2 fw-bold text-secondary">Layanan <span class="text-warning">Katering</span> Kami</h2>
                <p class="mt-3 text-secondary max-w-2xl mx-auto">Pilih layanan katering sesuai kebutuhan Anda</p>
            </div>
            <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-{{ count($services) > 2 ? '3' : count($services) }} g-3 items-stretch">
                @forelse($services as $service)
                    @php
                        $serviceUrl = $service->tipe_layanan === 'acara'
                            ? route('pelanggan.acara.lokasi')
                            : route('pelanggan.harian.lokasi');
                        $bgImage = $service->tipe_layanan === 'acara'
                            ? asset('images/acara.jpg')
                            : asset('images/harian.png');
                    @endphp
                    <a href="{{ $serviceUrl }}" class="group position-relative rounded-2xl border border-secondary hover:-translate-y-1 shadow-sm d-flex flex-column h-100 overflow-hidden text-decoration-none transition-all duration-300" style="min-height: 250px;">
                        <!-- Background Image -->
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('{{ $bgImage }}'); background-size: cover; background-position: center; transition: transform 0.3s ease-in-out;"></div>
                        
                        <!-- Overlay Gelap -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-75 group-hover:opacity-50 transition-opacity duration-300"></div>
                        
                        <!-- Konten -->
                        <div class="p-6 flex-grow-1 d-flex flex-column justify-content-center text-center position-relative" style="z-index: 1;">
                            <h3 class="fs-2 fw-bold text-white mb-0" style="text-shadow: 2px 2px 8px rgba(0,0,0,0.7);">{{ $service->nama }}</h3>
                        </div>
                    </a>
                @empty
                    <div class="col-12 text-center py-12">
                        <h3 class="fs-4 fw-medium text-secondary mb-2">Belum Ada Layanan</h3>
                        <p class="text-secondary">Mohon maaf, layanan katering saat ini sedang tidak tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Tentang Dapur Aisyah Section -->
    <section id="about" class="py-16 bg-light section-item">
        <div class="container py-5">
            <div class="row align-items-center">
                <!-- Kolom Kiri: Gambar -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="w-100 rounded overflow-hidden shadow border border-secondary" style="height: 350px;">
                        <img src="{{ asset('images/tim_katering.png') }}" alt="Tentang Dapur Aisyah" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
                <!-- Kolom Kanan: Judul & Deskripsi -->
                <div class="col-lg-6 px-lg-5">
                    <h2 class="fs-1 fw-bold text-dark mb-4">
                        Tentang <span class="text-warning">Dapur Aisyah</span>
                    </h2>
                    <p class="text-secondary fs-5 leading-relaxed" style="text-align: justify; line-height: 1.8;">
                        Dapur Aisyah adalah penyedia layanan katering rumahan yang menyajikan hidangan berkualitas untuk kebutuhan harian maupun berbagai acara. Dengan bahan segar, cita rasa rumahan, dan pelayanan yang terpercaya, kami berkomitmen memberikan pengalaman terbaik di setiap sajian.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    @if($ulasan->count() > 0)
    <section id="testimonials" class="py-5 bg-white section-item">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fs-1 fw-bold text-dark">Apa Kata <span class="text-warning">Pelanggan</span></h2>
                <p class="text-secondary fs-5">Ulasan dari pelanggan kami</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div id="carouselTestimoni" class="carousel carousel-dark slide text-center" data-bs-ride="carousel">
                        <div class="carousel-inner p-4 py-5  shadow-sm" style="background-color: #f8f9fa;">
                            @foreach($ulasan as $key => $item)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <h4 class="fw-bolder text-dark mb-4" style="letter-spacing: 0.5px;">{{ $item->user->name ?? 'Pelanggan' }}</h4>
                                <p class="text-secondary fs-5 mb-0 px-md-5 px-3" style="font-style: italic; line-height: 1.6;">"{{ $item->komentar ?? 'Pelayanan sangat memuaskan!' }}"</p>
                            </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselTestimoni" data-bs-slide="prev" style="width: 10%;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselTestimoni" data-bs-slide="next" style="width: 10%;">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <!-- <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="fs-2 md:fs-1 fw-bold text-white mb-4">Siap Memesan Katering?</h2>
            <p class="text-primary fs-5 mb-8">Pesan sekarang dan nikmati kemudahan layanan katering online kami.</p>
            @auth
                <a href="{{ route('pelanggan.harian.lokasi') }}" class="d-inline-block px-10 py-4 bg-white text-primary fw-bold rounded-pill shadow-lg hover:shadow-2xl">
                    Lihat Menu & Pesan →
                </a>
            @else
                <a href="{{ route('register') }}" class="d-inline-block px-10 py-4 bg-white text-primary fw-bold rounded-pill shadow-lg hover:shadow-2xl">
                    Daftar Gratis & Pesan →
                </a>
            @endauth
        </div>
    </section> -->
@endsection
