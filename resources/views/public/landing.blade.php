@extends('layouts.app')

@section('title', 'Katering Online Terpercaya Pontianak')

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-orange-200/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-amber-200/30 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <!-- <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-orange-100 text-orange-700 text-sm font-medium">
                        🔥 Promo Menarik Setiap Hari
                    </div> -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                        Katering <span class="bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent">Rumahan</span>
                        <br>Berkualitas Premium
                    </h1>
                    <p class="text-lg text-gray-600 max-w-md">
                        Nikmati masakan rumahan berkualitas untuk kebutuhan harian dan acara kantor Anda. Pesan mudah, bayar aman, antar cepat.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('customer.products') }}" class="px-8 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-full shadow-lg shadow-orange-200 hover:shadow-xl hover:shadow-orange-300 transform hover:scale-105 transition-all">
                                Pesan Sekarang →
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-full shadow-lg shadow-orange-200 hover:shadow-xl hover:shadow-orange-300 transform hover:scale-105 transition-all">
                                Pesan Sekarang →
                            </a>
                        @endauth
                        <a href="#services" class="px-8 py-3.5 bg-white text-orange-600 font-semibold rounded-full border-2 border-orange-200 hover:border-orange-400 transition-all">
                            Lihat Menu
                        </a>
                    </div>
                    <div class="flex items-center space-x-6 pt-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600">500+</p>
                            <p class="text-xs text-gray-500">Pesanan</p>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600">4.8⭐</p>
                            <p class="text-xs text-gray-500">Rating</p>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600">100%</p>
                            <p class="text-xs text-gray-500">Halal</p>
                        </div>
                    </div>
                </div>
                <div class="relative hidden md:block">
                    <div class="relative w-full aspect-square max-w-lg mx-auto">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-400 to-amber-400 rounded-3xl rotate-6 opacity-20"></div>
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-300 to-amber-300 rounded-3xl -rotate-3 opacity-20"></div>
                        <div class="relative bg-gradient-to-br from-orange-100 to-amber-100 rounded-3xl p-8 flex items-center justify-center">
                            <span class="text-9xl">🍲</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                                <div class="flex text-yellow-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
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
    <section class="py-16 bg-gradient-to-r from-orange-500 to-amber-500">
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
    </section>
@endsection
