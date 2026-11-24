<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\DetailPesanan;
use App\Models\ItemKeranjang;
use App\Models\Kategori;
use App\Models\Keranjang;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FoundationSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_semua_tabel_dan_kolom_sesuai_erd(): void
    {
        $expectedColumns = [
            'users' => ['id', 'nama', 'email', 'password', 'no_hp', 'foto_profil', 'role', 'status_akun', 'created_at', 'updated_at'],
            'alamat' => ['id', 'user_id', 'label', 'detail_alamat', 'created_at', 'updated_at'],
            'kategori' => ['id', 'nama_kategori', 'created_at', 'updated_at'],
            'menu' => ['id', 'kategori_id', 'nama', 'deskripsi', 'harga', 'harga_promo', 'foto', 'status_ketersediaan', 'rating', 'created_at', 'updated_at'],
            'keranjang' => ['id', 'user_id', 'created_at', 'updated_at'],
            'item_keranjang' => ['id', 'keranjang_id', 'menu_id', 'qty', 'catatan', 'subtotal', 'created_at', 'updated_at'],
            'meja' => ['id', 'nomor_meja', 'qr_code', 'created_at', 'updated_at'],
            'pesanan' => ['id', 'user_id', 'meja_id', 'metode_penerimaan', 'metode_pembayaran', 'status', 'alasan_tolak', 'total_harga', 'estimasi_waktu_ambil', 'created_at', 'updated_at'],
            'detail_pesanan' => ['id', 'pesanan_id', 'menu_id', 'qty', 'catatan', 'subtotal', 'created_at', 'updated_at'],
            'pembayaran' => ['id', 'pesanan_id', 'metode', 'status_pembayaran', 'jumlah', 'created_at', 'updated_at'],
        ];

        foreach ($expectedColumns as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} harus ada.");
            $actualColumns = Schema::getColumnListing($table);
            $this->assertEqualsCanonicalizing($columns, $actualColumns, "Kolom tabel {$table} harus sesuai ERD.");
        }

        $this->assertFalse(Schema::hasTable('cache'));
        $this->assertFalse(Schema::hasTable('jobs'));
        $this->assertFalse(Schema::hasTable('sessions'));
        $this->assertFalse(Schema::hasTable('password_reset_tokens'));
    }

    public function test_seeder_mengisi_data_awal_dapur_bucik_dengan_benar(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, User::count());
        $this->assertSame(4, Kategori::count());
        $this->assertSame(7, Menu::count());
        $this->assertSame(3, Meja::count());

        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertSame('aktif', $admin->status_akun);
        $this->assertTrue(Hash::check('password', $admin->password));

        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $this->assertSame('pelanggan', $budi->role);
        $this->assertSame('aktif', $budi->status_akun);

        $ismail = User::where('email', 'ismailpratama@gmail.com')->firstOrFail();
        $this->assertSame('pelanggan', $ismail->role);
        $this->assertSame('diblokir', $ismail->status_akun);

        $sayurAsem = Menu::where('nama', 'Sayur Asem')->firstOrFail();
        $this->assertSame('habis', $sayurAsem->status_ketersediaan);
        $this->assertSame('12000.00', $sayurAsem->harga);

        $nasiKuning = Menu::where('nama', 'Nasi Kuning Bumbu Rempah')->firstOrFail();
        $this->assertSame('20000.00', $nasiKuning->harga);
        $this->assertSame('15000.00', $nasiKuning->harga_promo);
    }

    public function test_relasi_eloquent_antar_seluruh_model_berjalan_lancar(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $menu = Menu::where('nama', 'Ayam Goreng Lengkuas')->firstOrFail();
        $meja = Meja::firstOrFail();

        $alamat = Alamat::create([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'detail_alamat' => 'Jl. Melati No. 10, Jakarta Selatan',
        ]);

        $keranjang = Keranjang::create([
            'user_id' => $user->id,
        ]);

        $itemKeranjang = ItemKeranjang::create([
            'keranjang_id' => $keranjang->id,
            'menu_id' => $menu->id,
            'qty' => 2,
            'catatan' => 'Sambal dipisah',
            'subtotal' => 50000.00,
        ]);

        $pesanan = Pesanan::create([
            'user_id' => $user->id,
            'meja_id' => $meja->id,
            'metode_penerimaan' => 'dine-in',
            'metode_pembayaran' => 'qris',
            'total_harga' => 50000.00,
        ]);

        $detailPesanan = DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $menu->id,
            'qty' => 2,
            'catatan' => 'Sambal dipisah',
            'subtotal' => 50000.00,
        ]);

        $pembayaran = Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'metode' => 'qris',
            'jumlah' => 50000.00,
        ]);

        $this->assertTrue($user->alamat->contains($alamat));
        $this->assertTrue($alamat->user->is($user));

        $this->assertTrue($user->keranjang->is($keranjang));
        $this->assertTrue($keranjang->user->is($user));
        $this->assertTrue($keranjang->itemKeranjang->contains($itemKeranjang));
        $this->assertTrue($itemKeranjang->keranjang->is($keranjang));
        $this->assertTrue($itemKeranjang->menu->is($menu));

        $this->assertTrue($menu->kategori->is(Kategori::where('nama_kategori', 'Masakan Rumah')->firstOrFail()));
        $this->assertTrue($menu->kategori->menu->contains($menu));
        $this->assertTrue($menu->itemKeranjang->contains($itemKeranjang));
        $this->assertTrue($menu->detailPesanan->contains($detailPesanan));

        $this->assertTrue($user->pesanan->contains($pesanan));
        $this->assertTrue($pesanan->user->is($user));
        $this->assertTrue($pesanan->meja->is($meja));
        $this->assertTrue($meja->pesanan->contains($pesanan));
        $this->assertTrue($pesanan->detailPesanan->contains($detailPesanan));
        $this->assertTrue($detailPesanan->pesanan->is($pesanan));
        $this->assertTrue($detailPesanan->menu->is($menu));
        $this->assertTrue($pesanan->pembayaran->is($pembayaran));
        $this->assertTrue($pembayaran->pesanan->is($pesanan));
    }
}
