<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\DetailPesanan;
use App\Models\ItemKeranjang;
use App\Models\Kategori;
use App\Models\Keranjang;
use App\Models\Menu;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_register_otomatis_set_role_pelanggan_dan_status_aktif(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Pelanggan Baru',
            'email' => 'baru@contoh.com',
            'no_hp' => '081233344455',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
            'status_akun' => 'diblokir',
        ]);

        $response->assertRedirect(route('user.home'));

        $user = User::where('email', 'baru@contoh.com')->firstOrFail();
        $this->assertSame('pelanggan', $user->role);
        $this->assertSame('aktif', $user->status_akun);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_ditolak_jika_status_akun_diblokir(): void
    {
        $blockedResponse = $this->from('/login')->post('/login', [
            'email' => 'ismailpratama@gmail.com',
            'password' => 'password',
        ]);

        $blockedResponse->assertRedirect('/login');
        $blockedResponse->assertSessionHasErrors('email');
        $this->assertGuest();

        $activeResponse = $this->post('/login', [
            'email' => 'budisantoso@gmail.com',
            'password' => 'password',
        ]);

        $activeResponse->assertRedirect(route('user.home'));
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect(route('user.home'));
        $this->assertGuest();
    }

    public function test_middleware_active_user_mengeluarkan_user_yang_diblokir(): void
    {
        $ismail = User::where('email', 'ismailpratama@gmail.com')->firstOrFail();

        $response = $this->actingAs($ismail)->get('/profil');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_edit_profil_dan_crud_alamat_termasuk_otorisasi_pemilik(): void
    {
        Storage::fake('public');

        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();

        $photo = UploadedFile::fake()->image('avatar.jpg');

        $this->actingAs($budi)->put('/profil', [
            'nama' => 'Budi Santoso Updated',
            'email' => 'percobaan-ubah@contoh.com',
            'no_hp' => '081200001111',
            'foto_profil' => $photo,
        ])->assertRedirect(route('user.profil.index', ['tab' => 'data-diri']));

        $budi->refresh();
        $this->assertSame('Budi Santoso Updated', $budi->nama);
        $this->assertSame('budisantoso@gmail.com', $budi->email);
        $this->assertNotNull($budi->foto_profil);
        $this->assertStringEndsWith('.webp', (string) $budi->foto_profil);
        Storage::disk('public')->assertExists($budi->foto_profil);

        $this->actingAs($budi)->put('/profil/password', [
            'current_password' => 'password',
            'password' => 'baru12345',
            'password_confirmation' => 'baru12345',
        ])->assertRedirect(route('user.profil.index', ['tab' => 'ubah-sandi']));

        $this->assertTrue(Hash::check('baru12345', $budi->fresh()->password));

        $this->actingAs($budi)->post('/profil/alamat', [
            'label' => 'Rumah',
            'detail_alamat' => 'Jl. Kenanga No. 10',
        ])->assertRedirect(route('user.profil.index', ['tab' => 'buku-alamat']));

        $alamat = Alamat::where('user_id', $budi->id)->firstOrFail();

        $this->actingAs($budi)->put("/profil/alamat/{$alamat->id}", [
            'label' => 'Kantor',
            'detail_alamat' => 'Jl. Sudirman Kav. 5',
        ])->assertRedirect(route('user.profil.index', ['tab' => 'buku-alamat']));

        $this->assertSame('Kantor', $alamat->fresh()->label);

        $this->actingAs($admin)->put("/profil/alamat/{$alamat->id}", [
            'label' => 'Hack',
            'detail_alamat' => 'Hack',
        ])->assertForbidden();

        $this->actingAs($budi)->delete("/profil/alamat/{$alamat->id}")
            ->assertRedirect(route('user.profil.index', ['tab' => 'buku-alamat']));

        $this->assertDatabaseMissing('alamat', ['id' => $alamat->id]);
    }

    public function test_katalog_filter_dan_qr_meja_disimpan_ke_session(): void
    {
        $cemilan = Kategori::where('nama_kategori', 'Cemilan Tradisional')->firstOrFail();

        $response = $this->get("/menu?meja=1&kategori_id={$cemilan->id}&q=Pisang&urut=harga_asc");

        $response->assertStatus(200);
        $response->assertSee('Pisang Goreng Madu');
        $response->assertSee('Meja 1 (Area Dekat Jendela)');
        $response->assertSessionHas('selected_meja_id', 1);
    }

    public function test_keranjang_menghitung_harga_promo_dan_menolak_menu_habis(): void
    {
        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $nasiKuning = Menu::where('nama', 'Nasi Kuning Bumbu Rempah')->firstOrFail();
        $sayurAsem = Menu::where('nama', 'Sayur Asem')->firstOrFail();

        $this->actingAs($budi)->post('/keranjang/item', [
            'menu_id' => $nasiKuning->id,
            'qty' => 2,
            'catatan' => 'Tanpa bawang',
        ])->assertRedirect();

        $item = ItemKeranjang::firstOrFail();
        $this->assertSame(2, $item->qty);
        $this->assertSame('30000.00', $item->subtotal);

        $this->actingAs($budi)->patch("/keranjang/item/{$item->id}", [
            'qty' => 3,
        ])->assertRedirect(route('user.keranjang.index'));

        $this->assertSame('45000.00', $item->fresh()->subtotal);

        $this->actingAs($budi)->post('/keranjang/item', [
            'menu_id' => $sayurAsem->id,
            'qty' => 1,
        ])->assertSessionHas('error');

        $this->assertSame(1, ItemKeranjang::count());

        $this->actingAs($budi)->delete("/keranjang/item/{$item->id}")
            ->assertRedirect(route('user.keranjang.index'));

        $this->assertSame(0, ItemKeranjang::count());
    }

    public function test_checkout_transaksi_simulasi_bayar_dan_lacak_riwayat(): void
    {
        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();
        $ayam = Menu::where('nama', 'Ayam Goreng Lengkuas')->firstOrFail();
        $nasiKuning = Menu::where('nama', 'Nasi Kuning Bumbu Rempah')->firstOrFail();

        $alamat = Alamat::create([
            'user_id' => $budi->id,
            'label' => 'Rumah',
            'detail_alamat' => 'Jl. Melati No. 5',
        ]);

        // Simpan meja via QR scan terlebih dahulu
        $this->actingAs($budi)->get('/menu?meja=2')->assertSessionHas('selected_meja_id', 2);

        $this->actingAs($budi)->post('/keranjang/item', [
            'menu_id' => $ayam->id,
            'qty' => 2,
            'catatan' => 'Sambal dipisah',
        ]);
        $this->actingAs($budi)->post('/keranjang/item', [
            'menu_id' => $nasiKuning->id,
            'qty' => 1,
        ]);

        // Halaman checkout otomatis memilih meja 2 dari session
        $this->actingAs($budi)->get('/checkout')
            ->assertStatus(200)
            ->assertSee('Terpilih dari Scan QR Meja');

        // Kurir mandiri tidak boleh bayar_di_tempat
        $this->actingAs($budi)->post('/checkout', [
            'metode_penerimaan' => 'kurir_mandiri',
            'alamat_id' => $alamat->id,
            'metode_pembayaran' => 'bayar_di_tempat',
        ])->assertSessionHasErrors('metode_pembayaran');

        // Checkout dine-in dengan meja 2 & QRIS
        $checkoutRes = $this->actingAs($budi)->post('/checkout', [
            'metode_penerimaan' => 'dine-in',
            'meja_id' => 2,
            'metode_pembayaran' => 'qris',
        ]);

        $pesanan = Pesanan::where('user_id', $budi->id)->latest()->firstOrFail();
        $checkoutRes->assertRedirect(route('user.pesanan.konfirmasi', $pesanan->id));
        $checkoutRes->assertSessionMissing('selected_meja_id');

        $this->assertSame(2, $pesanan->meja_id);
        $this->assertSame('menunggu_konfirmasi', $pesanan->status);
        $this->assertSame('65000.00', $pesanan->total_harga);
        $this->assertSame(2, DetailPesanan::where('pesanan_id', $pesanan->id)->count());

        $pembayaran = Pembayaran::where('pesanan_id', $pesanan->id)->firstOrFail();
        $this->assertSame('menunggu', $pembayaran->status_pembayaran);
        $this->assertSame('65000.00', $pembayaran->jumlah);

        $keranjang = Keranjang::where('user_id', $budi->id)->firstOrFail();
        $this->assertSame(0, $keranjang->itemKeranjang()->count());

        // Simulasi Saya Sudah Bayar
        $this->actingAs($budi)->post("/pesanan/{$pesanan->id}/bayar")
            ->assertRedirect(route('user.pesanan.lacak', $pesanan->id));

        $this->assertSame('lunas', $pembayaran->fresh()->status_pembayaran);

        // Pelacakan & Riwayat hanya untuk pemilik pesanan
        $this->actingAs($budi)->get("/pesanan/{$pesanan->id}/lacak")
            ->assertStatus(200)
            ->assertSee('Ayam Goreng Lengkuas')
            ->assertSee('Meja 2 (Area Tengah)');

        $this->actingAs($budi)->get('/riwayat')
            ->assertStatus(200)
            ->assertSee('#DB-'.str_pad((string) $pesanan->id, 4, '0', STR_PAD_LEFT));

        $this->actingAs($admin)->get("/pesanan/{$pesanan->id}/lacak")->assertForbidden();
    }
}
