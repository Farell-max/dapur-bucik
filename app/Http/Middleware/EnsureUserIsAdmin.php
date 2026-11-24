<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan hanya pengguna aktif dengan role admin yang dapat mengakses route /admin/*.
 */
class EnsureUserIsAdmin
{
    /**
     * Memeriksa autentikasi, status akun, dan role admin sebelum meneruskan request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if ($user->status_akun === 'diblokir') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Akun admin ini sedang diblokir.']);
        }

        if ($user->role !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk administrator Dapur Bucik.');
        }

        return $next($request);
    }
}
