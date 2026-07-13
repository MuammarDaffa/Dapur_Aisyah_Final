<x-guest-layout>
    <!-- Judul Lupa Password -->
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-4">Lupa Password</h2>

    <!-- Deskripsi singkat dalam Bahasa Indonesia -->
    <div class="mb-6 text-sm text-gray-600 text-center leading-relaxed">
        Lupa password akun Anda? Tidak masalah. Masukkan alamat email yang terdaftar dan kami akan mengirimkan tautan untuk mereset password Anda.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Input Email -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Button Kirim Tautan Reset Password -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:ring-orange-500 py-3 text-base">
                Kirim Tautan Reset Password
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
