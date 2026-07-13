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
            transition: all 250ms ease-in-out;
        }
        .hero-btn:hover {
            background-color: #ea580c; /* slightly darker orange */
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
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
    <section id="hero" class="relative overflow-hidden flex items-center justify-center text-center hero-section w-full section-item">
        <!-- Background Container -->
        <div class="absolute inset-0 z-0">
            <div id="hero-bg-1" class="absolute inset-0 hero-bg bg-cover bg-center"></div>
            <div id="hero-bg-2" class="absolute inset-0 hero-bg bg-cover bg-center opacity-0"></div>
            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-black/45 z-10"></div>
        </div>

        <!-- Content Container -->
        <div class="relative z-20 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center h-full">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight tracking-tight mb-4 max-w-3xl">
                Katering Rumahan Berkualitas Premium
            </h1>
            <p class="text-sm sm:text-base lg:text-lg text-gray-200 max-w-xl mb-8 leading-relaxed">
                Nikmati masakan rumahan berkualitas untuk kebutuhan harian atau acara.
            </p>
            <div>
                <a href="#services" class="inline-block px-8 py-3.5 font-bold rounded-full cursor-pointer hero-btn text-base shadow-sm scroll-to-services">
                    Pesan Sekarang
                </a>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = [
                "{{ asset('images/hero-1.jpg') }}",
                "{{ asset('images/hero-2.jpg') }}",
                "{{ asset('images/hero-3.jpg') }}",
                "{{ asset('images/hero-4.jpg') }}",
                "{{ asset('images/hero-5.jpg') }}"
            ];
            
            let currentIndex = 0;
            const bg1 = document.getElementById('hero-bg-1');
            const bg2 = document.getElementById('hero-bg-2');
            
            if (bg1 && bg2 && images.length > 0) {
                // Initialize first background
                bg1.style.backgroundImage = `url('${images[0]}')`;
                
                let activeBg = bg1;
                let inactiveBg = bg2;
                
                setInterval(function() {
                    currentIndex = (currentIndex + 1) % images.length;
                    
                    // Set background of inactive element
                    inactiveBg.style.backgroundImage = `url('${images[currentIndex]}')`;
                    
                    // Fade in inactive element and fade out active element
                    inactiveBg.style.opacity = '1';
                    activeBg.style.opacity = '0';
                    
                    // Swap active/inactive references
                    const temp = activeBg;
                    activeBg = inactiveBg;
                    inactiveBg = temp;
                }, 5000);
            }

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
            const sections = document.querySelectorAll('section.section-item[id]');

            // Smooth Scroll on Link Click
            mainNavLinks.forEach(link => {
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

                // If scrolled near bottom of page, activate 'about'
                if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight - 50) {
                    const aboutSection = document.getElementById('about');
                    if (aboutSection) {
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Layanan <span class="text-orange-500">Katering</span> Kami</h2>
                <p class="mt-3 text-gray-500 max-w-2xl mx-auto">Pilih layanan katering sesuai kebutuhan Anda</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ count($services) > 2 ? '3' : count($services) }} gap-8 items-stretch">
                @forelse($services as $service)
                    @php
                        $serviceUrl = $service->isEvent()
                            ? (auth()->check() ? route('customer.event.service', $service) : route('register'))
                            : (auth()->check() ? route('customer.products', ['service' => $service->id]) : route('register'));
                        $icon = $service->isDaily() ? '🍱' : ($service->isEvent() ? '🎉' : '🍽️');
                    @endphp
                    <a href="{{ $serviceUrl }}" class="group relative bg-white rounded-2xl border border-gray-100 hover:shadow-xl hover:shadow-orange-100 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full overflow-hidden">
                        @if($service->image)
                            <div class="h-56 w-full overflow-hidden relative shrink-0">
                                <img src="{{ Storage::url($service->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                                <h3 class="absolute bottom-4 left-4 text-2xl font-bold text-white mb-0 drop-shadow-md">{{ $service->name }}</h3>
                            </div>
                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <p class="text-sm text-gray-600 mb-6 line-clamp-3 leading-relaxed flex-1">{{ $service->description }}</p>
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                                    <span class="text-lg font-bold text-orange-600">
                                        Mulai Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                    </span>
                                    @if($service->isEvent())
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full font-medium">Event</span>
                                    @else
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">Tersedia</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="p-6 bg-gradient-to-br from-orange-50 to-amber-50 flex-1 flex flex-col justify-between h-full">
                                <div>
                                    <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-amber-500 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform shadow-sm">
                                        {{ $icon }}
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $service->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-6 line-clamp-3 leading-relaxed">{{ $service->description }}</p>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-orange-100/60 mt-auto">
                                    <span class="text-lg font-bold text-orange-600">
                                        Mulai Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                    </span>
                                    @if($service->isEvent())
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full font-medium">Event</span>
                                    @else
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">Tersedia</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-8">
                        Belum ada layanan tersedia.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Tentang Dapur Aisyah Section -->
    <section id="about" class="py-16 bg-gray-50 section-item">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Kolom Kiri: Gambar -->
                <div class="w-full h-64 sm:h-80 lg:h-96 rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                    <img src="{{ asset('images/katering_team.jpg') }}" alt="Tentang Dapur Aisyah" class="w-full h-full object-cover">
                </div>
                <!-- Kolom Kanan: Judul & Deskripsi -->
                <div class="flex flex-col justify-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Tentang <span class="text-orange-500">Dapur Aisyah</span>
                    </h2>
                    <p class="text-gray-600 text-base sm:text-lg leading-relaxed text-justify">
                        Dapur Aisyah adalah penyedia layanan katering rumahan yang menyajikan hidangan berkualitas untuk kebutuhan harian maupun berbagai acara. Dengan bahan segar, cita rasa rumahan, dan pelayanan yang terpercaya, kami berkomitmen memberikan pengalaman terbaik di setiap sajian.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    @if($reviews->count() > 0)
    <section id="testimonials" class="py-16 bg-white section-item">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Apa Kata <span class="text-orange-500">Pelanggan</span></h2>
                <p class="mt-3 text-gray-500">Ulasan dari pelanggan setia kami</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $reviews->count() >= 3 ? '3' : ($reviews->count() == 2 ? '2' : '1') }} gap-8 max-w-{{ $reviews->count() >= 3 ? '7xl' : ($reviews->count() == 2 ? '5xl' : '3xl') }} mx-auto items-stretch">
                @foreach($reviews as $review)
                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-7 border border-orange-100 shadow-sm hover:shadow-md transition-all flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-11 h-11 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center text-white font-bold text-base shadow-sm shrink-0">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-base">{{ $review->user->name ?? 'Pelanggan' }}</p>
                                    <p class="text-xs text-orange-500 font-medium mt-0.5">{{ $review->order->cateringService->name ?? 'Pelanggan Setia' }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-4 leading-relaxed">{{ $review->comment ?? 'Pelayanan sangat memuaskan!' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <!-- <section class="py-16 bg-gradient-to-r from-orange-500 to-amber-500">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap Memesan Katering?</h2>
            <p class="text-orange-100 text-lg mb-8">Pesan sekarang dan nikmati kemudahan layanan katering online kami.</p>
            @auth
                <a href="{{ route('customer.products') }}" class="inline-block px-10 py-4 bg-white text-orange-600 font-bold rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all">
                    Lihat Menu & Pesan →
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-block px-10 py-4 bg-white text-orange-600 font-bold rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all">
                    Daftar Gratis & Pesan →
                </a>
            @endauth
        </div>
    </section> -->
@endsection
