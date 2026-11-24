<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\ItemKeranjang;
use App\Models\Keranjang;
use App\Models\Menu;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendRoutesPreviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan seluruh 12 rute sisi pengguna dapat diakses dan dirender tanpa error.
     */
    public function test_seluruh_rute_pengguna_mengembalikan_status_200(): void
    {
        $this->seed(DatabaseSeeder::class);

        $guestRoutes = [
            '/' => 'Menu Terlaris Minggu Ini',
            '/login' => 'Masuk dengan Google',
            '/register' => 'Buat Akun Pelanggan',
            '/forgot-password' => 'Lupa Kata Sandi',
            '/menu' => 'Katalog Menu Dapur Bucik',
            '/menu/1' => 'Ayam Goreng Lengkuas',
        ];

        foreach ($guestRoutes as $uri => $expectedText) {
            $response = $this->get($uri);
            $response->assertStatus(200);
            $response->assertSee($expectedText, false);
        }

        $user = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $menu = Menu::firstOrFail();

        $keranjang = Keranjang::create(['user_id' => $user->id]);
        ItemKeranjang::create([
            'keranjang_id' => $keranjang->id,
            'menu_id' => $menu->id,
            'qty' => 2,
            'catatan' => 'Sambal dipisah',
            'subtotal' => 50000,
        ]);

        $pesanan = Pesanan::create([
            'user_id' => $user->id,
            'meja_id' => 1,
            'metode_penerimaan' => 'dine-in',
            'metode_pembayaran' => 'qris',
            'status' => 'dimasak',
            'total_harga' => 50000,
        ]);

        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $menu->id,
            'qty' => 2,
            'subtotal' => 50000,
        ]);

        Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'metode' => 'qris',
            'status_pembayaran' => 'menunggu',
            'jumlah' => 50000,
        ]);

        $authRoutes = [
            '/dashboard' => 'Buku Alamat',
            '/keranjang' => 'Ringkasan Nota',
            '/checkout' => 'Nota Pesanan',
            "/pesanan/konfirmasi/{$pesanan->id}" => 'Cara Pembayaran QRIS',
            "/pesanan/{$pesanan->id}/lacak" => 'Info Penerimaan',
            '/riwayat' => 'Riwayat Pesanan Saya',
            '/profil' => 'Buku Alamat',
        ];

        foreach ($authRoutes as $uri => $expectedText) {
            $response = $this->actingAs($user)->get($uri);
            $response->assertStatus(200);
            $response->assertSee($expectedText, false);
        }
    }

    /**
     * Memastikan seluruh 8 rute sisi admin dapat diakses oleh akun admin tanpa error.
     */
    public function test_seluruh_rute_admin_mengembalikan_status_200(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/admin/login')
            ->assertStatus(200)
            ->assertSee('Login Admin — Dapur Bucik', false);

        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();

        $adminRoutes = [
            '/admin/dashboard' => 'Menu Paling Dicari',
            '/admin/menu' => 'Manajemen Menu Dapur',
            '/admin/pesanan' => 'Perlu Diproses',
            '/admin/ongkir' => 'Kurir Mandiri',
            '/admin/meja' => 'Meja 1 (Area Dekat Jendela)',
            '/admin/pelanggan' => 'Buku Pelanggan',
            '/admin/laporan' => 'Ringkasan Penjualan',
        ];

        foreach ($adminRoutes as $uri => $expectedText) {
            $response = $this->actingAs($admin)->get($uri);
            $response->assertStatus(200);
            $response->assertSee($expectedText, false);
        }
    }
}
