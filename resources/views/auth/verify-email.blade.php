<x-guest-layout>
    <!-- Judul Verifikasi Email -->
    <h2 class="text-2xl font-bold text-center text-gray-900 mb-4">Verifikasi Email</h2>

    <!-- Deskripsi singkat dalam Bahasa Indonesia -->
    <div class="mb-6 text-sm text-gray-600 text-center leading-relaxed">
        Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan ulang.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg text-center border border-green-200">
            Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.
        </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Button Kirim Ulang Email Verifikasi -->
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
            @csrf
            <x-primary-button class="w-full sm:w-auto justify-center bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:ring-orange-500 py-2.5">
                Kirim Ulang Email Verifikasi
            </x-primary-button>
        </form>

        <!-- Link Keluar -->
        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center sm:text-right">
            @csrf
            <button type="submit" class="underline text-sm font-medium text-gray-600 hover:text-orange-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
