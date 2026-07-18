<x-guest-layout>
    <!-- Judul Reset Password -->
    <h2 class="fs-3 fw-bold text-center text-secondary mb-6">Reset Password</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token & Email (Hidden) -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

        <!-- Error untuk email/token jika ada -->
        <x-input-error :messages="$errors->get('email')" class="mb-4 text-center" />

        <!-- Password Baru -->
        <div>
            <x-input-label for="password" value="Password Baru" />
            <x-text-input id="password" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm" type="password" name="password" required autofocus autocomplete="new-password" placeholder="Masukkan password baru Anda" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru Anda" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Button Simpan Password -->
        <div class="mt-6">
            <x-primary-button class="w-100 justify-content-center bg-primary text-white hover:bg-primary text-white focus:bg-primary text-white active:bg-primary text-white py-3 text-base">
                Simpan Password
            </x-primary-button>
        </div>

        <!-- Link Kembali ke Masuk -->
        <div class="mt-6 text-center border-t border border-secondary pt-4">
            <a href="{{ route('login') }}" class="fs-6 fw-medium text-primary hover:text-primary underline">
                &larr; Kembali ke Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
