<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotSuspended
{
    /**
     * Handle an incoming request.
     *
     * Memblokir user yang statusnya 'suspended' atau 'pending_verification'
     * dari mengakses keranjang, checkout, dan fitur order.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status_suspend === 'suspended') {
            return redirect()->route('customer.profile.edit')
                ->with('error', '⛔ Akun Anda ditangguhkan karena terindikasi data tidak valid. Silakan perbarui nomor HP Anda untuk mengajukan verifikasi ulang.');
        }

        if ($user && $user->status_suspend === 'pending_verification') {
            return redirect()->route('customer.profile.edit')
                ->with('error', '⏳ Nomor HP baru Anda sedang dalam proses verifikasi oleh Admin. Mohon tunggu konfirmasi sebelum melanjutkan pesanan.');
        }

        return $next($request);
    }
}
