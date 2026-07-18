<x-guest-layout>
    <!-- Judul Daftar -->
    <h2 class="fs-3 fw-bold text-center text-secondary mb-6">Daftar</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Nomor Telepon -->
        <div class="mt-4">
            <x-input-label for="phone" value="Nomor Telepon" />
            <x-text-input id="phone" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm" type="text" name="phone" :value="old('phone')" required placeholder="Contoh: 081234567890" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />

            <x-text-input id="password" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Minimal 8 karakter" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />

            <x-text-input id="password_confirmation" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password di atas" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Button Daftar -->
        <div class="mt-6">
            <x-primary-button class="w-100 justify-content-center bg-primary text-white hover:bg-primary text-white focus:bg-primary text-white active:bg-primary text-white py-3 text-base">
                Daftar
            </x-primary-button>
        </div>

        <!-- Link Sudah punya akun? Masuk -->
        <div class="mt-6 text-center border-t border border-secondary pt-4">
            <p class="fs-6 text-secondary">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="fw-medium text-primary hover:text-primary underline">
                    Masuk
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
