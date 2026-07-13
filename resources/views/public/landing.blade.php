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
    </style>

    <!-- Hero Section -->
    <section class="relative overflow-hidden flex items-center justify-center text-center hero-section w-full">
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
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            }
        });
    </script>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Layanan <span class="text-orange-500">Katering</span> Kami</h2>
                <p class="mt-3 text-gray-500 max-w-2xl mx-auto">Pilih layanan katering sesuai kebutuhan Anda</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ count($services) > 2 ? '3' : count($services) }} gap-8">
                @forelse($services as $service)
                    @php
                        $serviceUrl = $service->isEvent()
                            ? (auth()->check() ? route('customer.event.service', $service) : route('register'))
                            : (auth()->check() ? route('customer.products', ['service' => $service->id]) : route('register'));
                        $icon = $service->isDaily() ? '🍱' : ($service->isEvent() ? '🎉' : '🍽️');
                    @endphp
                    <a href="{{ $serviceUrl }}" class="group relative bg-white rounded-2xl border border-gray-100 hover:shadow-xl hover:shadow-orange-100 transition-all duration-300 transform hover:-translate-y-1 block overflow-hidden">
                        @if($service->image)
                            <div class="h-48 w-full overflow-hidden relative">
                                <img src="{{ Storage::url($service->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                                <h3 class="absolute bottom-4 left-4 text-2xl font-bold text-white mb-0 drop-shadow-md">{{ $service->name }}</h3>
                            </div>
                            <div class="p-6">
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $service->description }}</p>
                                <div class="flex items-center justify-between">
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
                            <div class="p-8 bg-gradient-to-br from-orange-50 to-amber-50 h-full">
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-amber-500 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                                    {{ $icon }}
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $service->description }}</p>
                                <div class="flex items-center justify-between">
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

    <!-- Best Sellers Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Menu <span class="text-orange-500">Terpopuler</span></h2>
                <p class="mt-3 text-gray-500">Menu favorit pelanggan kami</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($bestSellers as $product)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="relative h-48 bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-6xl">🍛</span>
                            @endif
                            @if($product->is_best_seller)
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">🔥 Best Seller</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-orange-500 font-medium mb-1">{{ $product->cateringService->name ?? '' }}</p>
                            <h3 class="font-bold text-gray-900 mb-2">{{ $product->name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-orange-600">{{ $product->formatted_price }}</span>
                                @auth
                                    <form action="{{ route('customer.cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="p-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-colors shadow-sm hover:shadow-md">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-8">
                        Belum ada produk populer.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    @if($reviews->count() > 0)
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Apa Kata <span class="text-orange-500">Pelanggan</span></h2>
                <p class="mt-3 text-gray-500">Ulasan dari pelanggan setia kami</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($reviews as $review)
                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $review->user->name ?? 'Pelanggan' }}</p>
                                <p class="text-xs text-orange-500 font-medium">{{ $review->order->cateringService->name ?? 'Pelanggan Setia' }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-3">{{ $review->comment ?? 'Pelayanan sangat memuaskan!' }}</p>
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
