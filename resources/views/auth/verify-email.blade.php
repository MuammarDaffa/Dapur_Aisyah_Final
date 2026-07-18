<x-guest-layout>
    <!-- Judul Verifikasi Email -->
    <h2 class="fs-3 fw-bold text-center text-secondary mb-4">Verifikasi Email</h2>

    <!-- Deskripsi singkat dalam Bahasa Indonesia -->
    <div class="mb-6 fs-6 text-secondary text-center leading-relaxed">
        Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan ulang.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 fw-medium fs-6 text-success bg-success text-white p-3 rounded text-center border border-green-200">
            Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.
        </div>
    @endif

    <div class="mt-6 d-flex d-flex-column sm:d-flex-row align-items-center justify-content-between g-3">
        <!-- Button Kirim Ulang Email Verifikasi -->
        <form method="POST" action="{{ route('verification.send') }}" class="w-100 sm:w-auto">
            @csrf
            <x-primary-button class="w-100 sm:w-auto justify-content-center bg-primary text-white hover:bg-primary text-white focus:bg-primary text-white active:bg-primary text-white py-2.5">
                Kirim Ulang Email Verifikasi
            </x-primary-button>
        </form>

        <!-- Link Keluar -->
        <form method="POST" action="{{ route('logout') }}" class="w-100 sm:w-auto text-center sm:text-end">
            @csrf
            <button type="submit" class="underline fs-6 fw-medium text-secondary hover:text-primary rounded focus: -2">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
