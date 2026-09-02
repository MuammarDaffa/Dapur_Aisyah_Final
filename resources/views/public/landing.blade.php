@extends('layouts.app')

@section('title', 'Katering Organik & Premium Pontianak')

@section('content')
    <style>
        /* Modern Organic Gourmet - Landing Styles */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-color: var(--bg-cream);
            position: relative;
            overflow: hidden;
            padding-top: 100px;
        }
        
        .organic-blob-1 {
            position: absolute;
            top: -10%; left: -10%;
            width: 600px; height: 600px;
            background: var(--forest-green);
            opacity: 0.03;
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            animation: morph 15s ease-in-out infinite alternate;
            z-index: 0;
        }

        .organic-blob-2 {
            position: absolute;
            bottom: -20%; right: -5%;
            width: 800px; height: 800px;
            background: var(--primary-terracotta);
            opacity: 0.03;
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            animation: morph 20s ease-in-out infinite alternate-reverse;
            z-index: 0;
        }

        @keyframes morph {
            0% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: rotate(0deg); }
            100% { border-radius: 70% 30% 50% 50% / 30% 30% 70% 70%; transform: rotate(10deg); }
        }

        .hero-title {
            font-size: clamp(3rem, 6vw, 5.5rem);
            line-height: 1.1;
            font-weight: 800;
            color: var(--forest-green);
            letter-spacing: -0.03em;
        }
        .hero-subtitle {
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            color: #475569;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.8;
        }

        /* Bento Features */
        .bento-feature {
            background: white;
            border-radius: var(--bento-radius);
            padding: 40px;
            height: 100%;
            box-shadow: var(--soft-shadow);
            transition: all 0.4s ease;
        }
        .bento-feature:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(44, 74, 59, 0.1);
        }

        .service-bento {
            position: relative;
            border-radius: var(--bento-radius);
            overflow: hidden;
            background: white;
            box-shadow: var(--soft-shadow);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            border: 2px solid transparent;
        }
        .service-bento:hover {
            transform: scale(1.02);
            border-color: var(--primary-terracotta);
            box-shadow: 0 25px 50px rgba(224, 93, 54, 0.15);
        }
        .service-bento img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.7s ease;
        }
        .service-bento:hover img {
            transform: scale(1.05);
        }
        .service-bento-content {
            padding: 30px;
            background: white;
        }

        /* Abstract Image Frame */
        .image-frame-organic {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(44,74,59,0.15);
            transition: all 0.5s ease;
        }
        
        .section-item { scroll-margin-top: 100px; }
    </style>

    <!-- Oversized Hero Section -->
    <section id="hero" class="hero-section section-item">
        <div class="organic-blob-1"></div>
        <div class="organic-blob-2"></div>

        <div class="container position-relative z-1 text-center">
            <div class="d-inline-flex align-items-center gap-2 bg-white rounded-pill px-4 py-2 shadow-sm mb-4 border border-light animate__animated animate__fadeInDown">
                <span class="d-flex align-items-center justify-content-center bg-primary-mc rounded-circle" style="width: 10px; height: 10px;"></span>
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Katering Premium Pontianak</span>
            </div>
            
            <h1 class="hero-title mb-4 animate__animated animate__fadeInUp">
                Citarasa Rumah,<br>
                <span class="text-primary-mc" style="font-style: italic; font-weight: 700;">Standar Gourmet.</span>
            </h1>
            
            <p class="hero-subtitle mb-5 animate__animated animate__fadeInUp animate__delay-1s">
                Bukan sekadar makanan, ini adalah simfoni gizi dan rasa. Disiapkan khusus dengan bahan segar terbaik untuk kesehatan dan kebahagiaan Anda.
            </p>
            
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center animate__animated animate__fadeInUp animate__delay-1s">
                <a href="#services" class="btn btn-primary-mc fs-5 text-decoration-none shadow-lg">
                    Mulai Eksplorasi <i class="fa-solid fa-arrow-down ms-2"></i>
                </a>
            </div>

            <!-- Floating Hero Elements -->
            <div class="mt-5 pt-4 position-relative mx-auto" style="max-width: 900px; height: 400px;">
                <!-- Bento Style Hero Images -->
                <div class="row g-4 h-100">
                    <div class="col-md-7 h-100">
                        <div class="rounded-4 overflow-hidden h-100 shadow-lg" style="border-radius: 32px !important;">
                            <img src="{{ asset('images/katering.png') }}" class="w-100 h-100" style="object-fit: cover;" alt="Premium Food">
                        </div>
                    </div>
                    <div class="col-md-5 h-100 d-flex flex-column gap-4">
                        <div class="bg-white rounded-4 p-4 shadow-sm h-50 d-flex flex-column justify-content-center align-items-center text-center" style="border-radius: 32px !important;">
                            <h2 class="display-4 fw-bold text-primary-mc mb-0">100%</h2>
                            <p class="text-secondary fw-bold mb-0">Halal & Higienis</p>
                        </div>
                        <div class="text-white rounded-4 p-4 shadow-sm h-50 d-flex flex-column justify-content-center align-items-center text-center" style="border-radius: 32px !important; background-color: var(--forest-green);">
                            <i class="fa-solid fa-leaf fs-1 mb-2 text-warning"></i>
                            <p class="fw-bold mb-0 fs-5">Bahan Segar Pilihan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Script for smooth scroll -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollLinks = document.querySelectorAll('a[href^="#"]');
            scrollLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href').substring(1);
                    if(!targetId) return;
                    
                    const targetEl = document.getElementById(targetId);
                    if (targetEl) {
                        e.preventDefault();
                        const navOffset = 100;
                        const targetPosition = targetEl.getBoundingClientRect().top + window.pageYOffset - navOffset;
                        window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                    }
                });
            });
        });
    </script>

    <!-- Services Section (Bento Cards) -->
    <section id="services" class="py-5 section-item mt-5">
        <div class="container mx-auto px-4 pt-5">
            <div class="text-center mb-5 pb-3">
                <span class="text-primary-mc fw-bold tracking-wider text-uppercase d-block mb-2">Pilihan Cerdas</span>
                <h2 class="fs-1 fw-bold text-dark">Layanan Katering <span class="text-primary-mc">Kami</span></h2>
            </div>
            
            <div class="services-container">
                @forelse($services as $service)
                    @php
                        $serviceUrl = $service->tipe_layanan === 'acara' ? route('pelanggan.acara.lokasi') : route('pelanggan.harian.lokasi');
                        $bgImage = $service->tipe_layanan === 'acara' ? asset('images/acara.jpg') : asset('images/harian.png');
                        $icon = $service->tipe_layanan === 'acara' ? 'fa-calendar-star' : 'fa-sun';
                        $desc = $service->tipe_layanan === 'acara' 
                            ? 'Sajikan hidangan istimewa tanpa repot untuk momen spesial, rapat penting, atau perayaan keluarga Anda.' 
                            : 'Menu bergizi yang berganti setiap hari. Solusi makan siang cerdas untuk gaya hidup produktif Anda.';
                    @endphp
                    
                    <div class="row align-items-center mb-5 {{ $loop->last ? '' : 'pb-5' }}">
                        <!-- Image Column -->
                        <div class="col-lg-6 mb-4 mb-lg-0 {{ $loop->even ? 'order-lg-2' : '' }}">
                            <div class="position-relative overflow-hidden shadow-sm" style="border-radius: 32px; height: 450px;">
                                <img src="{{ $bgImage }}" alt="{{ $service->nama }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                <div class="position-absolute top-0 start-0 m-4 bg-white px-4 py-2 rounded-pill shadow-sm fw-bold text-dark d-flex align-items-center gap-2">
                                    <div class="bg-primary-mc rounded-circle" style="width:10px; height:10px;"></div>
                                    {{ ucfirst($service->tipe_layanan) }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Text Column -->
                        <div class="col-lg-6 {{ $loop->even ? 'order-lg-1 pe-lg-5' : 'ps-lg-5' }}">
                            <h3 class="display-5 fw-bold text-dark mb-4">{{ $service->nama }}</h3>
                            <p class="text-secondary fs-5 mb-5" style="line-height: 1.8;">{{ $desc }}</p>
                            
                            <a href="{{ $serviceUrl }}" class="btn btn-outline-dark rounded-pill fw-bold px-5 py-3 border-2 hover-bg-dark d-inline-flex align-items-center gap-3" style="transition: all 0.3s ease;">
                                Lihat Menu <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-4 shadow-sm border border-light mx-auto" style="max-width: 500px; border-radius: 32px !important;">
                            <div class="bg-light rounded-circle d-flex justify-content-center align-items-center mx-auto mb-4" style="width:80px; height:80px;">
                                <i class="fa-solid fa-box-open fs-2 text-secondary"></i>
                            </div>
                            <h3 class="fs-4 fw-bold text-dark mb-2">Belum Ada Layanan</h3>
                            <p class="text-secondary mb-0">Kami sedang meracik menu terbaik untuk Anda. Silakan kembali nanti.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Filosofi Section (Bento Grid) -->
    <section id="about" class="py-5 section-item">
        <div class="container py-5">
            <div class="row g-4 align-items-center">
                <!-- Left: Big Bento Image -->
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <div class="image-frame-organic mx-auto">
                        <img src="{{ asset('images/tim_katering.png') }}" alt="Filosofi Dapur Aisyah" class="w-100" style="height: 600px; object-fit: cover;">
                    </div>
                </div>
                <!-- Right: Bento Details -->
                <div class="col-lg-7 ps-lg-5">
                    <span class="text-primary-mc fw-bold tracking-wider text-uppercase d-block mb-2">Filosofi Kami</span>
                    <h2 class="fs-1 fw-bold text-dark mb-4">
                        Lebih dari Sekadar <span style="color: var(--forest-green);">Makanan</span>
                    </h2>
                    <p class="text-secondary fs-5 mb-5" style="line-height: 1.8;">
                        Di Dapur Aisyah, kami percaya bahwa makanan yang baik adalah fondasi hari yang luar biasa. Kami memadukan resep otentik warisan keluarga dengan standar kebersihan modern.
                    </p>
                    
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="bento-feature p-4">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-leaf fs-4 text-primary-mc"></i>
                                </div>
                                <h4 class="fw-bold fs-5 text-dark mb-2">Bahan Organik</h4>
                                <p class="text-secondary small mb-0">Sayuran segar dari petani lokal pilihan.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bento-feature p-4">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-temperature-half fs-4 text-primary-mc"></i>
                                </div>
                                <h4 class="fw-bold fs-5 text-dark mb-2">Dimasak Sempurna</h4>
                                <p class="text-secondary small mb-0">Teknik memasak sehat tanpa mengurangi cita rasa.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bento-feature p-4">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-box-open fs-4 text-primary-mc"></i>
                                </div>
                                <h4 class="fw-bold fs-5 text-dark mb-2">Kemasan Aman</h4>
                                <p class="text-secondary small mb-0">Menggunakan wadah ramah lingkungan & higienis (Food Grade).</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bento-feature p-4" style="background-color: var(--forest-green); color: white;">
                                <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-truck-fast fs-4 text-warning"></i>
                                </div>
                                <h4 class="fw-bold fs-5 text-white mb-2">Tepat Waktu</h4>
                                <p class="text-light opacity-75 small mb-0">Diantar hangat saat jam makan Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    @if($ulasan->count() > 0)
    <section id="testimonials" class="py-5 section-item mb-5" style="background-color: white;">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-primary-mc fw-bold tracking-wider text-uppercase d-block mb-2">Cerita Pelanggan</span>
                <h2 class="fs-1 fw-bold text-dark">Apa Kata <span style="color: var(--forest-green);">Mereka?</span></h2>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div id="carouselTestimoni" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner pb-5">
                            @foreach($ulasan as $key => $item)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <div class="text-center mx-auto" style="max-width: 800px;">
                                    <i class="fa-solid fa-quote-left display-3 text-light mb-4"></i>
                                    <p class="fs-3 fw-bold text-dark mb-5" style="line-height: 1.6; letter-spacing: -0.5px;">
                                        "{{ $item->komentar ?? 'Pelayanan sangat memuaskan dan rasa makanannya lezat!' }}"
                                    </p>
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white fs-4 fw-bold" style="width: 60px; height: 60px; background-color: var(--primary-terracotta);">
                                            {{ strtoupper(substr($item->user->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div class="text-start">
                                            <h5 class="fw-bold text-dark mb-1">{{ $item->user->name ?? 'Pelanggan Setia' }}</h5>
                                            <div class="text-warning small">
                                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-center gap-3 mt-2">
                            <button class="btn btn-light rounded-circle shadow-sm" type="button" data-bs-target="#carouselTestimoni" data-bs-slide="prev" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-arrow-left text-dark"></i>
                            </button>
                            <button class="btn btn-light rounded-circle shadow-sm" type="button" data-bs-target="#carouselTestimoni" data-bs-slide="next" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-arrow-right text-dark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

@endsection
