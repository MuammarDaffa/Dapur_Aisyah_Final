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

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bungee+Inline&display=swap');

        /* Custom Hero Styles Matching Image */
        .hero {
            width: 100%;
            min-height: 100vh;
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('{{ asset('images/acara.jpg') }}') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .hero-content {
            z-index: 10;
            padding: 0 15px;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(3rem, 8vw, 6rem);
            font-family: 'Bungee Inline', cursive;
            color: #ffffff;
            letter-spacing: 2px;
            line-height: 1.2;
            text-transform: uppercase;
            text-shadow: 2px 4px 10px rgba(0,0,0,0.5);
        }

        .hero-buttons {
            margin-top: 40px;
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .hero .btn-order {
            background-color: #ce1212;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 16px;
            padding: 12px 35px;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 2px solid #ce1212;
        }

        .hero .btn-order:hover {
            background-color: transparent;
            color: #ce1212;
        }

        .hero .btn-menu {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 16px;
            padding: 12px 35px;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 2px solid #ffffff;
            backdrop-filter: blur(5px);
        }

        .hero .btn-menu:hover {
            background-color: #ffffff;
            color: #37373f;
        }

        /* Carousel Navigation Controls - Image Style */
        .hero-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
            pointer-events: none;
        }

        .hero-nav button {
            background: transparent;
            border: none;
            color: white;
            font-size: 2rem;
            pointer-events: auto;
            transition: 0.3s;
        }

        .hero-nav button:hover {
            color: #ce1212;
            transform: scale(1.2);
        }

        /* Carousel Dots */
        .hero-dots {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .hero-dots span {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            background: transparent;
            cursor: pointer;
            transition: 0.3s;
        }

        .hero-dots span.active {
            background: white;
            box-shadow: 0 0 10px white;
        }

        /* Yummy Section Title Exact Styles (Kept for other sections) */
        .section-title h2 {
            font-size: 14px;
            font-weight: 500;
            padding: 0;
            line-height: 1px;
            margin: 0 0 5px 0;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #8a8a8a;
            font-family: 'Inter', sans-serif;
        }
        .section-title p {
            margin: 0;
            font-size: 36px;
            font-weight: 700;
            font-family: 'Amatic SC', sans-serif;
            color: #37373f;
        }
        .section-title p span {
            color: #ce1212;
        }
    </style>

    <section id="hero" class="hero section-item">
        <div class="hero-content">
            <h1 class="animate__animated animate__fadeInDown">
                CITARASA RUMAH<br>STANDAR GOURMET
            </h1>
            <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-1s">
                <a href="#services" class="btn-order">Order Now</a>
                <a href="#menu" class="btn-menu">View Menu</a>
            </div>
        </div>

        <!-- Decorative elements to match the image carousel look -->
        <div class="hero-nav">
            <button><i class="fa-solid fa-chevron-left"></i></button>
            <button><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="hero-dots">
            <span class="active"></span>
            <span></span>
            <span></span>
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
            <div class="container section-title text-center mb-5 pb-3">
                <h2>Pilihan Cerdas</h2>
                <p>Layanan Katering <span>Kami</span></p>
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

    <style>
        /* New Menu Section Layout Matching Image */
        .menu-tab-custom {
            border: none;
            border-bottom: 2px solid #e9ecef;
            background: transparent;
            border-radius: 0;
            padding: 10px 30px;
            margin: 0 10px;
            color: #37373f;
            display: flex;
            align-items: center;
            opacity: 0.7;
            transition: 0.3s;
        }
        .menu-tab-custom.active {
            border-bottom: 3px solid #ce1212 !important;
            opacity: 1;
            background: transparent !important;
            color: #37373f !important;
        }
        .menu-tab-custom i {
            color: #ce1212;
            font-size: 2.5rem;
            margin-right: 15px;
            transition: 0.3s;
        }
        .menu-tab-custom:hover {
            opacity: 1;
        }
        .menu-tab-custom h6 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }
        .menu-tab-custom small {
            font-size: 13px;
            color: #8a8a8a;
        }

        .menu-list-item {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .menu-list-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .menu-list-content {
            width: 100%;
            padding-left: 20px;
        }
        .menu-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .menu-list-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }
        .menu-list-price {
            font-size: 20px;
            font-weight: 700;
            color: #ce1212;
            margin: 0;
        }
        .menu-list-desc {
            font-size: 14px;
            color: #8a8a8a;
            font-style: italic;
            margin: 0;
        }
    </style>

    <!-- Menu Section -->
    <section id="menu" class="menu section-item py-5 bg-white">
        <div class="container py-5">
            <div class="section-title text-center mb-5">
                <h2>Menu Pilihan</h2>
                <p>Lihat Hidangan <span>Spesial Kami</span></p>
            </div>

            <ul class="nav nav-tabs justify-content-center mb-5 border-0" id="menu-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link menu-tab-custom active" id="pills-harian-tab" data-bs-toggle="pill" data-bs-target="#pills-harian" type="button" role="tab" aria-selected="true">
                        <i class="fa-solid fa-mug-hot"></i>
                        <div class="text-start">
                            <small class="d-block">Pilihan</small>
                            <h6>Harian</h6>
                        </div>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link menu-tab-custom" id="pills-acara-tab" data-bs-toggle="pill" data-bs-target="#pills-acara" type="button" role="tab" aria-selected="false">
                        <i class="fa-solid fa-burger"></i>
                        <div class="text-start">
                            <small class="d-block">Spesial</small>
                            <h6>Acara</h6>
                        </div>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- Tab Harian -->
                <div class="tab-pane fade show active" id="pills-harian" role="tabpanel" aria-labelledby="pills-harian-tab">
                    <div class="row">
                        @forelse($menuHarian as $menu)
                        <div class="col-lg-6">
                            <div class="menu-list-item">
                                <img src="{{ asset('storage/' . $menu->gambar) }}" onerror="this.src='{{ asset('images/harian.png') }}'" class="menu-list-img" alt="{{ $menu->nama_menu }}">
                                <div class="menu-list-content">
                                    <div class="menu-list-header">
                                        <h4 class="menu-list-title">{{ $menu->nama_menu }}</h4>
                                        <h4 class="menu-list-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</h4>
                                    </div>
                                    <p class="menu-list-desc">{{ Str::limit($menu->deskripsi, 80) }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-secondary py-4">Belum ada menu harian.</div>
                        @endforelse
                    </div>
                </div>
                <!-- Tab Acara -->
                <div class="tab-pane fade" id="pills-acara" role="tabpanel" aria-labelledby="pills-acara-tab">
                    <div class="row">
                        @forelse($menuAcara as $menu)
                        <div class="col-lg-6">
                            <div class="menu-list-item">
                                <img src="{{ asset('storage/' . $menu->gambar) }}" onerror="this.src='{{ asset('images/acara.jpg') }}'" class="menu-list-img" alt="{{ $menu->nama_menu }}">
                                <div class="menu-list-content">
                                    <div class="menu-list-header">
                                        <h4 class="menu-list-title">{{ $menu->nama_menu }}</h4>
                                        <h4 class="menu-list-price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</h4>
                                    </div>
                                    <p class="menu-list-desc">{{ Str::limit($menu->deskripsi, 80) }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-secondary py-4">Belum ada menu acara.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .gallery .gallery-item {
            overflow: hidden;
            border-right: 3px solid #fff;
            border-bottom: 3px solid #fff;
        }
        .gallery .gallery-item img {
            transition: all ease-in-out 0.4s;
            height: 300px;
            object-fit: cover;
            width: 100%;
        }
        .gallery .gallery-item:hover img {
            transform: scale(1.1);
        }
    </style>

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section-item py-5">
        <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">
            <div class="section-title text-center mb-5">
                <h2>Galeri</h2>
                <p>Cek Dapur & <span>Acara Kami</span></p>
            </div>
            
            <div class="row g-0">
                <div class="col-lg-3 col-md-4">
                    <div class="gallery-item">
                        <img src="{{ asset('images/katering.png') }}" class="img-fluid" alt="">
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="gallery-item">
                        <img src="{{ asset('images/harian.png') }}" class="img-fluid" alt="">
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="gallery-item">
                        <img src="{{ asset('images/acara.jpg') }}" class="img-fluid" alt="">
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="gallery-item">
                        <img src="{{ asset('images/tim_katering.png') }}" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .about .book-a-table {
            text-align: center;
            padding: 30px;
            background: #ffffff;
            box-shadow: 0px 2px 25px rgba(0, 0, 0, 0.08);
            margin-top: -60px;
            position: relative;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .about .book-a-table h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #37373f;
        }
        .about .book-a-table p {
            color: #ce1212;
            font-weight: 700;
            font-size: 24px;
            margin: 0;
        }
        .about .content ul {
            list-style: none;
            padding: 0;
        }
        .about .content ul li {
            padding: 0 0 8px 0;
            display: flex;
            align-items: flex-start;
        }
        .about .content ul li i {
            color: #ce1212;
            font-size: 20px;
            margin-right: 10px;
            margin-top: -2px;
        }
        .about .content p {
            color: #4f4f5a;
        }
        .about .play-btn {
            width: 94px;
            height: 94px;
            background: radial-gradient(#ce1212 50%, rgba(206, 18, 18, 0.4) 52%);
            border-radius: 50%;
            display: block;
            position: absolute;
            left: calc(50% - 47px);
            top: calc(50% - 47px);
            overflow: hidden;
            transition: all 0.4s;
        }
        .about .play-btn::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateX(-40%) translateY(-50%);
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-left: 15px solid #fff;
            z-index: 100;
            transition: all 0.4s cubic-bezier(0.55, 0.055, 0.675, 0.19);
        }
        .about .play-btn:hover {
            transform: scale(1.1);
        }
    </style>

    <!-- About Section -->
    <section id="about" class="about section-item py-5">
      <div class="container">
        
        <div class="section-title text-center mb-5">
            <h2>Tentang Kami</h2>
            <p>Lebih dari Sekadar <span>Makanan</span></p>
        </div>

        <div class="row gy-4">
          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
            <img src="{{ asset('images/tim_katering.png') }}" class="img-fluid mb-4 w-100" style="height: 500px; object-fit: cover;" alt="">
            <div class="book-a-table">
              <h3>Pusat Bantuan & Pemesanan</h3>
              <p>+62 812 3456 7890</p>
            </div>
          </div>
          <div class="col-lg-5" data-aos="fade-up" data-aos-delay="250">
            <div class="content ps-0 ps-lg-5">
              <p class="fst-italic">
                Di Dapur Aisyah, kami percaya bahwa makanan yang baik adalah fondasi hari yang luar biasa. Kami memadukan resep otentik warisan keluarga dengan standar kebersihan modern.
              </p>
              <ul>
                <li><i class="fa-solid fa-circle-check"></i> <span><strong>Bahan Organik:</strong> Sayuran segar dari petani lokal pilihan.</span></li>
                <li><i class="fa-solid fa-circle-check"></i> <span><strong>Dimasak Sempurna:</strong> Teknik memasak sehat tanpa mengurangi cita rasa.</span></li>
                <li><i class="fa-solid fa-circle-check"></i> <span><strong>Kemasan Aman:</strong> Menggunakan wadah ramah lingkungan & higienis (Food Grade).</span></li>
                <li><i class="fa-solid fa-circle-check"></i> <span><strong>Tepat Waktu:</strong> Diantar hangat saat jam makan Anda.</span></li>
              </ul>
              <p>
                Kepuasan pelanggan adalah prioritas utama kami. Nikmati setiap suapan yang kami persiapkan dengan sepenuh hati.
              </p>
              <div class="position-relative mt-4">
                <img src="{{ asset('images/katering.png') }}" class="img-fluid w-100" style="height: 250px; object-fit: cover;" alt="">
                <a href="#" class="play-btn"></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <style>
        .testimonials .testimonial-item {
            box-sizing: content-box;
            text-align: left;
            min-height: 200px;
        }
        .testimonials .testimonial-content {
            border-left: 3px solid #ce1212;
            padding-left: 30px;
        }
        .testimonials .testimonial-content p {
            font-style: italic;
            font-size: 18px;
            color: #37373f;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .testimonials .testimonial-content h3 {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 5px 0;
            color: #37373f;
            text-transform: uppercase;
        }
        .testimonials .testimonial-content h4 {
            font-size: 12px;
            color: #999;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }
        .testimonials .testimonial-content .stars i {
            color: #ffc107;
            font-size: 14px;
        }
        /* Custom Carousel Indicators */
        .testimonials .carousel-indicators {
            bottom: -30px;
        }
        .testimonials .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #d3d3d3;
            margin: 0 5px;
            border: none;
            opacity: 1;
        }
        .testimonials .carousel-indicators .active {
            background-color: #ce1212;
        }
    </style>

    <!-- Testimonials -->
    @if($ulasan->count() > 0)
    <section id="testimonials" class="testimonials section-item py-5" style="background-color: #f2f2f2;">
        <div class="container py-5">
            <div class="section-title text-center mb-5">
                <h2>Testimoni</h2>
                <p>Apa Kata <span>Mereka?</span></p>
            </div>
            
            <div id="carouselTestimoni" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-indicators">
                    @foreach($ulasan as $key => $item)
                        <button type="button" data-bs-target="#carouselTestimoni" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}"></button>
                    @endforeach
                </div>
                
                <div class="carousel-inner pb-5">
                    @foreach($ulasan as $key => $item)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <div class="testimonial-item">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="testimonial-content">
                                        <p>
                                            <i class="fa-solid fa-quote-left quote-icon-left" style="color: #ce1212; font-size: 24px; margin-right: 10px;"></i>
                                            {{ $item->komentar ?? 'Pelayanan sangat memuaskan dan rasa makanannya lezat!' }}
                                            <i class="fa-solid fa-quote-right quote-icon-right" style="color: rgba(206,18,18,0.3); font-size: 24px; margin-left: 10px;"></i>
                                        </p>
                                        <h3>{{ $item->user->name ?? 'Pelanggan Setia' }}</h3>
                                        <h4>Pelanggan</h4>
                                        <div class="stars">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

@endsection
