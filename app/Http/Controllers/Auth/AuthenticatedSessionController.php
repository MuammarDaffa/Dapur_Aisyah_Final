<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->user()) {
            \App\Models\Keranjang::cleanupInvalidAndExpiredItems($request->user()->id);
        }

        if ($request->user()->isCustomer() && !$request->user()->hasVerifiedEmail()) {
            return redirect(route('verification.notice'));
        }

        // Pulihkan pesanan tertunda jika ada di session (dari proses Masukkan ke Keranjang saat guest)
        if ($redirect = \App\Http\Controllers\Pelanggan\KeranjangController::restorePendingCart($request)) {
            return $redirect;
        }

        // Role-based redirect
        return match ($request->user()->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'owner' => redirect()->intended(route('owner.dashboard')),
            default => redirect()->intended(route('landing')),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
