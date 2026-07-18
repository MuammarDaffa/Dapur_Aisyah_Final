<x-guest-layout>
    <!-- Judul Konfirmasi Password -->
    <h2 class="fs-3 fw-bold text-center text-secondary mb-4">Konfirmasi Password</h2>

    <div class="mb-6 fs-6 text-secondary text-center leading-relaxed">
        Ini adalah area aman dari aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />

            <x-text-input id="password" class="d-block mt-1 w-100 border border-secondary focus:border border-primary rounded shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Masukkan password Anda" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="d-flex justify-content-end mt-6">
            <x-primary-button class="bg-primary text-white hover:bg-primary text-white focus:bg-primary text-white active:bg-primary text-white">
                Konfirmasi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
