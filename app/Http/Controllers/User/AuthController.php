<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller autentikasi pelanggan (Login, Register, Logout, dan Lupa Sandi).
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman masuk pelanggan.
     */
    public function login(): View
    {
        return view('user.auth.login');
    }

    /**
     * Memproses autentikasi pelanggan dan menolak akun berstatus diblokir.
     */
    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.']);
        }

        $user = $request->user();

        if ($user && $user->status_akun === 'diblokir') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda diblokir oleh admin. Silakan hubungi pengelola Dapur Bucik.']);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('user.home'))
            ->with('success', 'Selamat datang kembali, '.$user?->nama.'!');
    }

    /**
     * Menampilkan halaman pendaftaran akun pelanggan.
     */
    public function register(): View
    {
        return view('user.auth.register');
    }

    /**
     * Menyimpan akun pelanggan baru dengan role pelanggan dan status aktif.
     */
    public function storeRegister(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => $validated['password'],
            'role' => 'pelanggan',
            'status_akun' => 'aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('user.home')
            ->with('success', 'Pendaftaran berhasil! Selamat menikmati hidangan Dapur Bucik.');
    }

    /**
     * Menampilkan halaman lupa kata sandi pelanggan.
     */
    public function forgotPassword(): View
    {
        return view('user.auth.forgot-password');
    }

    /**
     * Mengeluarkan pelanggan dari sesi autentikasi aktif.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('user.home')
            ->with('success', 'Anda telah keluar dari akun.');
    }
}
