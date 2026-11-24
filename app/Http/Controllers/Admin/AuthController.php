<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller autentikasi khusus administrator Dapur Bucik.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman masuk administrator dapur.
     */
    public function login(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->role === 'admin' && $user->status_akun === 'aktif') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Memproses login admin dan memastikan hanya role admin aktif yang diizinkan masuk.
     */
    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau kata sandi admin tidak sesuai.']);
        }

        $user = $request->user();

        if (! $user || $user->role !== 'admin' || $user->status_akun === 'diblokir') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akses ditolak. Akun ini bukan administrator atau sedang diblokir.']);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('success', 'Selamat datang di Panel Admin, '.$user->nama.'.');
    }

    /**
     * Mengeluarkan administrator dari sesi aktif.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Anda telah keluar dari Panel Admin.');
    }
}
