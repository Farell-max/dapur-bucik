<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan pengguna yang sedang login memiliki status akun aktif (tidak diblokir admin).
 */
class EnsureUserIsActive
{
    /**
     * Memeriksa status akun pengguna pada setiap request terautentikasi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status_akun === 'diblokir') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda diblokir oleh admin. Silakan hubungi pengelola Dapur Bucik.']);
        }

        return $next($request);
    }
}
