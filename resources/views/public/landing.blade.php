@extends('layouts.app')

@section('title', 'Katering Online Terpercaya Pontianak')

@section('content')
    <style>
        html {
            scroll-behavior: smooth;
        }
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
            <h1 class="display-5 fw-bold text-white mb-4" style="max-width: 800px;">
                Katering Rumahan Berkualitas Premium
            </h1>
            <p class="fs-5 text-light mb-5" style="max-width: 600px;">
                Nikmati masakan rumahan berkualitas untuk kebutuhan harian atau acara.
            </p>
            <div>
                <a href="#services" class="btn btn-primary btn-lg rounded-0 px-5 py-3 fw-bold shadow scroll-to-services">
                    Pesan Sekarang
                </a>
            </div>
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
                <h2 class="fs-2 fw-bold text-secondary">Layanan <span class="text-primary">Katering</span> Kami</h2>
                <p class="mt-3 text-secondary max-w-2xl mx-auto">Pilih layanan katering sesuai kebutuhan Anda</p>
            </div>
            <div class="row row-cols-1 sm:row-cols-2 lg:row-cols-{{ count($services) > 2 ? '3' : count($services) }} g-3 items-stretch">
                @forelse($services as $service)
                    @php
                        $serviceUrl = $service->isEvent()
                            ? route('customer.event.service', $service)
                            : route('customer.produk', ['service' => $service->id]);
                    @endphp
                    <a href="{{ $serviceUrl }}" class="group position-relative bg-white rounded-2xl border border border-secondary hover:-translate-y-1 d-flex d-flex-column h-100 overflow-hidden">
                        @if($service->image)
                            <div class="w-100 overflow-hidden position-relative flex-shrink-0" style="height: 200px;">
                                <img src="{{ Storage::url($service->image) }}" class="w-100 h-100 object-fit-cover">
                                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);"></div>
                                <h3 class="position-absolute bottom-0 start-0 p-3 fs-4 fw-bold text-white mb-0">{{ $service->name }}</h3>
                            </div>
                            <div class="p-6 d-flex-1 d-flex d-flex-column justify-content-between">
                                <p class="fs-6 text-secondary mb-6 line-clamp-3 leading-relaxed d-flex-1">{{ $service->deskripsi }}</p>
                                <div class="d-flex align-items-center justify-content-between pt-4 border-t border border-secondary mt-auto">
                                    <span class="fs-5 fw-bold text-primary">
                                        Mulai Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                    </span>
                                    @if($service->isEvent())
                                        <span class="small bg-purple-100 text-purple-700 px-2 py-1 rounded-pill fw-medium">Event</span>
                                    @else
                                        <span class="small bg-success text-white text-success px-2 py-1 rounded-pill fw-medium">Tersedia</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="p-6 d-flex-1 d-flex d-flex-column justify-content-between h-100">
                                <div>
                                    <div style="width: 64px; height: 64px;" class="rounded-2xl d-flex align-items-center justify-content-center text-white mb-6 group- transition- shadow-sm">
                                        @if($service->isDaily())
                                            <svg style="width: 32px; height: 32px;" class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        @elseif($service->isEvent())
                                            <svg style="width: 32px; height: 32px;" class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                        @else
                                            <svg style="width: 32px; height: 32px;" class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                                        @endif
                                    </div>
                                    <h3 class="fs-4 fw-bold text-secondary mb-3">{{ $service->name }}</h3>
                                    <p class="fs-6 text-secondary mb-6 line-clamp-3 leading-relaxed">{{ $service->deskripsi }}</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-4 border-t border border-primary/60 mt-auto">
                                    <span class="fs-5 fw-bold text-primary">
                                        Mulai Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                    </span>
                                    @if($service->isEvent())
                                        <span class="small bg-purple-100 text-purple-700 px-2 py-1 rounded-pill fw-medium">Event</span>
                                    @else
                                        <span class="small bg-success text-white text-success px-2 py-1 rounded-pill fw-medium">Tersedia</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="col-span-full text-center text-secondary py-8">
                        Belum ada layanan tersedia.
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
                        Tentang <span class="text-primary">Dapur Aisyah</span>
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
                <h2 class="fs-1 fw-bold text-dark">Apa Kata <span class="text-primary">Pelanggan</span></h2>
                <p class="text-secondary fs-5">Ulasan dari pelanggan setia kami</p>
            </div>
            <div class="row justify-content-center g-4">
                @foreach($ulasan as $ulasan)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-primary shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 bg-primary" style="width: 48px; height: 48px;">
                                        {{ strtoupper(substr($ulasan->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold text-dark mb-0 fs-6">{{ $ulasan->user->name ?? 'Pelanggan' }}</h5>
                                        <small class="text-primary fw-medium">{{ $ulasan->pesanan->layananKatering->name ?? 'Pelanggan Setia' }}</small>
                                    </div>
                                </div>
                                <p class="card-text text-secondary mb-0" style="font-style: italic;">"{{ $ulasan->comment ?? 'Pelayanan sangat memuaskan!' }}"</p>
                            </div>
                        </div>
                    </div>
                @endforeach
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
                <a href="{{ route('customer.produk') }}" class="d-inline-block px-10 py-4 bg-white text-primary fw-bold rounded-pill shadow-lg hover:shadow-2xl">
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
