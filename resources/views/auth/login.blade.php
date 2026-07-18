<x-guest-layout>
    <!-- Judul Masuk -->
    <h2 class="fs-3 fw-bold text-center text-secondary mb-6">Masuk</h2>

    <!-- Session Status / Flash Message -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Checkbox Ingat Saya -->
        <div class="d-block mt-4">
            <label for="remember_me" class="d-inline-d-flex align-items-center">
                <input id="remember_me" type="checkbox" class="rounded border border-secondary text-primary shadow-sm" name="remember">
                <span class="ms-2 fs-6 text-secondary">Ingat Saya</span>
            </label>
        </div>

        <!-- Button Masuk & Link Lupa Password? -->
        <div class="d-flex align-items-center justify-content-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline fs-6 text-secondary hover:text-primary rounded focus: -2" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif

            <x-primary-button class="ms-3 bg-primary text-white hover:bg-primary text-white focus:bg-primary text-white active:bg-primary text-white">
                Masuk
            </x-primary-button>
        </div>

        <!-- Link Belum punya akun? Daftar -->
        <div class="mt-6 text-center border-t border border-secondary pt-4">
            <p class="fs-6 text-secondary">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="fw-medium text-primary hover:text-primary underline">
                    Daftar
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
