<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\MejaController as AdminMejaController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OngkirController as AdminOngkirController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\MenuController;
use App\Http\Controllers\User\PesananController;
use App\Http\Controllers\User\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Sisi Pengguna (Terhubung Database MySQL)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('user.home');
Route::get('/menu', [MenuController::class, 'index'])->name('user.menu.index');
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('user.menu.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [UserAuthController::class, 'login'])->name('login');
    Route::post('/login', [UserAuthController::class, 'authenticate'])->name('login.attempt');
    Route::get('/register', [UserAuthController::class, 'register'])->name('register');
    Route::post('/register', [UserAuthController::class, 'storeRegister'])->name('register.store');
    Route::get('/forgot-password', [UserAuthController::class, 'forgotPassword'])->name('password.request');
});

Route::post('/logout', [UserAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active.user'])->group(function (): void {
    Route::get('/dashboard', [ProfilController::class, 'index'])->name('user.dashboard');

    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('user.keranjang.index');
    Route::post('/keranjang/item', [KeranjangController::class, 'store'])->name('user.keranjang.store');
    Route::patch('/keranjang/item/{itemKeranjang}', [KeranjangController::class, 'update'])->name('user.keranjang.update');
    Route::delete('/keranjang/item/{itemKeranjang}', [KeranjangController::class, 'destroy'])->name('user.keranjang.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('user.checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('user.checkout.store');

    Route::get('/pesanan/konfirmasi/{pesanan?}', [PesananController::class, 'konfirmasi'])->name('user.pesanan.konfirmasi');
    Route::post('/pesanan/{pesanan}/bayar', [PesananController::class, 'simulasiBayar'])->name('user.pesanan.bayar');
    Route::get('/pesanan/{pesanan}/lacak', [PesananController::class, 'lacak'])->name('user.pesanan.lacak');
    Route::get('/riwayat', [PesananController::class, 'riwayat'])->name('user.riwayat.index');

    Route::get('/profil', [ProfilController::class, 'index'])->name('user.profil.index');
    Route::put('/profil', [ProfilController::class, 'updateProfile'])->name('user.profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('user.profil.password');
    Route::post('/profil/alamat', [ProfilController::class, 'storeAlamat'])->name('user.profil.alamat.store');
    Route::put('/profil/alamat/{alamat}', [ProfilController::class, 'updateAlamat'])->name('user.profil.alamat.update');
    Route::delete('/profil/alamat/{alamat}', [ProfilController::class, 'destroyAlamat'])->name('user.profil.alamat.destroy');
});

/*
|--------------------------------------------------------------------------
| Routes Sisi Admin (Terproteksi Middleware Role Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'authenticate'])->name('login.attempt');
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/menu', [AdminMenuController::class, 'index'])->name('menu.index');
        Route::post('/menu', [AdminMenuController::class, 'store'])->name('menu.store');
        Route::put('/menu/{menu}', [AdminMenuController::class, 'update'])->name('menu.update');
        Route::patch('/menu/{menu}/toggle', [AdminMenuController::class, 'toggleStatus'])->name('menu.toggle');
        Route::delete('/menu/{menu}', [AdminMenuController::class, 'destroy'])->name('menu.destroy');
        Route::post('/kategori', [AdminMenuController::class, 'storeKategori'])->name('kategori.store');

        Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan.index');
        Route::patch('/pesanan/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.update-status');
        Route::patch('/pesanan/{pesanan}/konfirmasi-bayar', [AdminPesananController::class, 'konfirmasiBayar'])->name('pesanan.konfirmasi-bayar');
        Route::get('/pesanan/{pesanan}/struk', [AdminPesananController::class, 'struk'])->name('pesanan.struk');

        Route::get('/ongkir', [AdminOngkirController::class, 'index'])->name('ongkir.index');

        Route::get('/meja', [AdminMejaController::class, 'index'])->name('meja.index');
        Route::post('/meja', [AdminMejaController::class, 'store'])->name('meja.store');
        Route::put('/meja/{meja}', [AdminMejaController::class, 'update'])->name('meja.update');
        Route::delete('/meja/{meja}', [AdminMejaController::class, 'destroy'])->name('meja.destroy');

        Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan.index');
        Route::patch('/pelanggan/{user}/toggle-status', [AdminPelangganController::class, 'toggleStatus'])->name('pelanggan.toggle-status');

        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/export/excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    });
});
