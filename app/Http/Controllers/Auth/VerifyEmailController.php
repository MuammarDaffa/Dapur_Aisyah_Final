<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            if ($redirect = \App\Http\Controllers\Pelanggan\KeranjangController::restorePendingCart($request)) {
                return $redirect;
            }
            return redirect()->intended(route($request->user()->getDashboardRouteName(), absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        if ($redirect = \App\Http\Controllers\Pelanggan\KeranjangController::restorePendingCart($request)) {
            return $redirect;
        }

        return redirect()->intended(route($request->user()->getDashboardRouteName(), absolute: false).'?verified=1');
    }
}
