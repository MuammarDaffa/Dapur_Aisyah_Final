<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUnverifiedCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isCustomer() && !auth()->user()->hasVerifiedEmail()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Alamat email Anda belum diverifikasi.'], 409);
            }
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
