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
        /* Yummy Hero Exact Styles */
        .hero {
            width: 100%;
            min-height: 100vh;
            position: relative;
            padding: 120px 0 60px 0;
            display: flex;
            align-items: center;
        }
        .hero h1 {
            margin: 0;
            font-size: 64px;
            font-weight: 700;
            font-family: 'Amatic SC', sans-serif;
            color: #37373f;
        }
        .hero p {
            color: #4f4f5a;
            margin: 15px 0 0 0;
            font-size: 20px;
        }
        .hero .btn-get-started {
            color: #ffffff;
            background: #ce1212;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 15px;
            letter-spacing: 1px;
            display: inline-block;
            padding: 12px 36px;
            border-radius: 50px;
            transition: 0.5s;
            box-shadow: 0 8px 28px rgba(206, 18, 18, 0.2);
            text-decoration: none;
        }
        .hero .btn-get-started:hover {
            background: rgba(206, 18, 18, 0.8);
            box-shadow: 0 8px 28px rgba(206, 18, 18, 0.45);
        }
        .hero .btn-watch-video {
            font-size: 16px;
            transition: 0.5s;
            margin-left: 25px;
            color: #37373f;
            font-weight: 600;
            text-decoration: none;
        }
        .hero .btn-watch-video i {
            color: #ce1212;
            font-size: 32px;
            transition: 0.3s;
            line-height: 0;
            margin-right: 8px;
        }
        .hero .btn-watch-video:hover {
            color: #ce1212;
        }
        .hero .hero-img img {
            animation: up-down 2s ease-in-out infinite alternate-reverse both;
        }
        @keyframes up-down {
            0% { transform: translateY(10px); }
            100% { transform: translateY(-10px); }
        }

        /* Yummy Section Title Exact Styles */
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
      <div class="container">
        <div class="row gy-4 justify-content-center justify-content-lg-between">
          <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h1 class="animate__animated animate__fadeInUp">Citarasa Rumah,<br>Standar Gourmet</h1>
            <p class="animate__animated animate__fadeInUp animate__delay-1s">Bukan sekadar makanan, ini adalah simfoni gizi dan rasa. Disiapkan khusus dengan bahan segar terbaik untuk kesehatan dan kebahagiaan Anda setiap harinya.</p>
            <div class="d-flex align-items-center mt-4 animate__animated animate__fadeInUp animate__delay-1s">
              <a href="#services" class="btn-get-started">Pesan Sekarang</a>
              <a href="#about" class="btn-watch-video d-flex align-items-center"><i class="fa-regular fa-circle-play"></i><span>Lihat Profil Kami</span></a>
            </div>
          </div>
          <div class="col-lg-5 order-1 order-lg-2 hero-img animate__animated animate__fadeInRight">
            <img src="{{ asset('images/katering.png') }}" class="img-fluid rounded-circle" alt="">
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
        /* Yummy Menu Exact Styles */
        .menu .nav-tabs {
            border: 0;
            justify-content: center;
        }
        .menu .nav-link {
            margin: 0 10px;
            padding: 10px 30px;
            transition: 0.3s;
            color: #37373f;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-bottom: 0;
            font-weight: 500;
        }
        .menu .nav-link.active {
            background: #ce1212;
            color: #fff;
            border-color: #ce1212;
        }
        .menu .menu-item {
            text-align: center;
            margin-bottom: 30px;
        }
        .menu .menu-img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
            height: 250px;
            object-fit: cover;
        }
        .menu .menu-item h4 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #37373f;
        }
        .menu .menu-item .price {
            font-size: 24px;
            font-weight: 700;
            color: #ce1212;
            margin: 0;
        }
        .menu .menu-item .ingredients {
            color: #8a8a8a;
            margin-bottom: 10px;
        }
    </style>

    <!-- Menu Section -->
    <section id="menu" class="menu section-item py-5 bg-light">
        <div class="container py-5">
            <div class="section-title text-center mb-5">
                <h2>Menu Pilihan</h2>
                <p>Lihat Hidangan <span>Spesial Kami</span></p>
            </div>

            <ul class="nav nav-tabs justify-content-center mb-5" id="menu-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-harian-tab" data-bs-toggle="pill" data-bs-target="#pills-harian" type="button" role="tab" aria-selected="true">Katering Harian</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-acara-tab" data-bs-toggle="pill" data-bs-target="#pills-acara" type="button" role="tab" aria-selected="false">Katering Acara</button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- Tab Harian -->
                <div class="tab-pane fade show active" id="pills-harian" role="tabpanel" aria-labelledby="pills-harian-tab">
                    <div class="row g-4">
                        @forelse($menuHarian as $menu)
                        <div class="col-lg-4 col-md-6">
                            <div class="menu-item">
                                <img src="{{ asset('storage/' . $menu->gambar) }}" onerror="this.src='{{ asset('images/harian.png') }}'" class="menu-img" alt="{{ $menu->nama_menu }}">
                                <h4>{{ $menu->nama_menu }}</h4>
                                <p class="ingredients">{{ Str::limit($menu->deskripsi, 60) }}</p>
                                <p class="price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-secondary">Belum ada menu harian.</div>
                        @endforelse
                    </div>
                </div>
                <!-- Tab Acara -->
                <div class="tab-pane fade" id="pills-acara" role="tabpanel" aria-labelledby="pills-acara-tab">
                    <div class="row g-4">
                        @forelse($menuAcara as $menu)
                        <div class="col-lg-4 col-md-6">
                            <div class="menu-item">
                                <img src="{{ asset('storage/' . $menu->gambar) }}" onerror="this.src='{{ asset('images/acara.jpg') }}'" class="menu-img" alt="{{ $menu->nama_menu }}">
                                <h4>{{ $menu->nama_menu }}</h4>
                                <p class="ingredients">{{ Str::limit($menu->deskripsi, 60) }}</p>
                                <p class="price">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-secondary">Belum ada menu acara.</div>
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
            text-align: center;
            min-height: 320px;
        }
        .testimonials .testimonial-item .testimonial-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            margin: 0 auto;
            background-color: #ce1212;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
        }
        .testimonials .testimonial-item h3 {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0 5px 0;
            color: #37373f;
        }
        .testimonials .testimonial-item h4 {
            font-size: 14px;
            color: #999;
            margin: 0;
        }
        .testimonials .testimonial-item .stars {
            margin: 10px 0;
        }
        .testimonials .testimonial-item .stars i {
            color: #ffc107;
            margin: 0 1px;
        }
        .testimonials .testimonial-item .quote-icon-left,
        .testimonials .testimonial-item .quote-icon-right {
            color: rgba(206, 18, 18, 0.4);
            font-size: 26px;
            line-height: 0;
        }
        .testimonials .testimonial-item p {
            font-style: italic;
            margin: 0 auto 15px auto;
            color: #4f4f5a;
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
            
            <div id="carouselTestimoni" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner pb-5">
                    @foreach($ulasan as $key => $item)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <div class="testimonial-item">
                            <div class="row gy-4 justify-content-center">
                                <div class="col-lg-6">
                                    <div class="testimonial-content">
                                        <p>
                                            <i class="fa-solid fa-quote-left quote-icon-left"></i>
                                            <span>{{ $item->komentar ?? 'Pelayanan sangat memuaskan dan rasa makanannya lezat!' }}</span>
                                            <i class="fa-solid fa-quote-right quote-icon-right"></i>
                                        </p>
                                        <h3>{{ $item->user->name ?? 'Pelanggan Setia' }}</h3>
                                        <h4>Pelanggan</h4>
                                        <div class="stars">
                                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 text-center">
                                    <div class="testimonial-img shadow">
                                        {{ strtoupper(substr($item->user->name ?? 'P', 0, 1)) }}
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
    </section>
    @endif

@endsection
