<x-guest-layout>
    <!-- Judul Konfirmasi Password -->
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-4">Konfirmasi Password</h2>

    <div class="mb-6 text-sm text-gray-600 text-center leading-relaxed">
        Ini adalah area aman dari aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />

            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Masukkan password Anda" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
            <x-primary-button class="bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:ring-orange-500">
                Konfirmasi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
