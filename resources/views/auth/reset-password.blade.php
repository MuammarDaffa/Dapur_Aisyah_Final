<x-guest-layout>
    <!-- Judul Reset Password -->
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-6">Reset Password</h2>

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
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" type="password" name="password" required autofocus autocomplete="new-password" placeholder="Masukkan password baru Anda" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru Anda" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Button Simpan Password -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:ring-orange-500 py-3 text-base">
                Simpan Password
            </x-primary-button>
        </div>

        <!-- Link Kembali ke Masuk -->
        <div class="mt-6 text-center border-t border-gray-100 pt-4">
            <a href="{{ route('login') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700 underline">
                &larr; Kembali ke Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
