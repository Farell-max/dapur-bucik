<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_hanya_admin_yang_dapat_mengakses_route_admin(): void
    {
        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();

        // Tamu diarahkan ke halaman login admin
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));

        // Pelanggan dilarang mengakses /admin/*
        $this->actingAs($budi)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($budi)->get('/admin/menu')->assertForbidden();
        $this->actingAs($budi)->get('/admin/pesanan')->assertForbidden();
        $this->actingAs($budi)->get('/admin/meja')->assertForbidden();
        $this->actingAs($budi)->get('/admin/pelanggan')->assertForbidden();
        $this->actingAs($budi)->get('/admin/laporan')->assertForbidden();

        // Pelanggan ditolak pada form login admin
        $this->post('/admin/login', [
            'email' => 'budisantoso@gmail.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        // Admin berhasil login dan logout
        $this->post('/admin/login', [
            'email' => 'admindapurbucik@gmail.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_dashboard_admin_menampilkan_angka_real_dari_database(): void
    {
        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();
        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $ayam = Menu::where('nama', 'Ayam Goreng Lengkuas')->firstOrFail();
        $nasgor = Menu::where('nama', 'Nasi Goreng Kampung')->firstOrFail();

        $selesai = Pesanan::create([
            'user_id' => $budi->id,
            'meja_id' => 1,
            'metode_penerimaan' => 'dine-in',
            'metode_pembayaran' => 'qris',
            'status' => 'selesai',
            'total_harga' => 50000,
        ]);
        DetailPesanan::create([
            'pesanan_id' => $selesai->id,
            'menu_id' => $ayam->id,
            'qty' => 2,
            'subtotal' => 50000,
        ]);
        Pembayaran::create([
            'pesanan_id' => $selesai->id,
            'metode' => 'qris',
            'status_pembayaran' => 'lunas',
            'jumlah' => 50000,
        ]);

        $aktif = Pesanan::create([
            'user_id' => $budi->id,
            'meja_id' => null,
            'metode_penerimaan' => 'pickup',
            'metode_pembayaran' => 'bayar_di_tempat',
            'status' => 'dimasak',
            'total_harga' => 20000,
        ]);
        DetailPesanan::create([
            'pesanan_id' => $aktif->id,
            'menu_id' => $nasgor->id,
            'qty' => 1,
            'subtotal' => 20000,
        ]);
        Pembayaran::create([
            'pesanan_id' => $aktif->id,
            'metode' => 'bayar_di_tempat',
            'status_pembayaran' => 'menunggu',
            'jumlah' => 20000,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('50.000');
        $response->assertSee('Ayam Goreng Lengkuas');
        $response->assertSee('2 porsi');
    }

    public function test_admin_crud_menu_kategori_upload_foto_dan_toggle_status(): void
    {
        Storage::fake('public');

        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();
        $kategori = Kategori::firstOrFail();

        // Tambah kategori baru
        $this->actingAs($admin)->post('/admin/kategori', [
            'nama_kategori' => 'Paket Spesial',
        ])->assertRedirect(route('admin.menu.index'));
        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Paket Spesial']);

        // Tambah menu dengan foto ke storage/app/public/menu
        $photo = UploadedFile::fake()->image('rendang.jpg');
        $this->actingAs($admin)->post('/admin/menu', [
            'kategori_id' => $kategori->id,
            'nama' => 'Rendang Daging Bucik',
            'deskripsi' => 'Rendang sapi empuk bumbu minang rumahan.',
            'harga' => 35000,
            'harga_promo' => 30000,
            'status_ketersediaan' => 'tersedia',
            'foto' => $photo,
        ])->assertRedirect(route('admin.menu.index'));

        $menu = Menu::where('nama', 'Rendang Daging Bucik')->firstOrFail();
        $this->assertStringStartsWith('menu/', (string) $menu->foto);
        $this->assertStringEndsWith('.webp', (string) $menu->foto);
        Storage::disk('public')->assertExists((string) $menu->foto);

        // Update menu
        $this->actingAs($admin)->put("/admin/menu/{$menu->id}", [
            'kategori_id' => $kategori->id,
            'nama' => 'Rendang Daging Spesial',
            'deskripsi' => 'Diperbarui dengan ekstra bumbu.',
            'harga' => 38000,
            'harga_promo' => null,
            'status_ketersediaan' => 'tersedia',
        ])->assertRedirect(route('admin.menu.index'));
        $this->assertSame('Rendang Daging Spesial', $menu->fresh()->nama);

        // Toggle cepat status_ketersediaan
        $this->actingAs($admin)->patch("/admin/menu/{$menu->id}/toggle")
            ->assertRedirect(route('admin.menu.index'));
        $this->assertSame('habis', $menu->fresh()->status_ketersediaan);

        // Hapus menu yang belum pernah dipesan beserta foto
        $fotoPath = (string) $menu->foto;
        $this->actingAs($admin)->delete("/admin/menu/{$menu->id}")
            ->assertRedirect(route('admin.menu.index'));
        $this->assertDatabaseMissing('menu', ['id' => $menu->id]);
        Storage::disk('public')->assertMissing($fotoPath);

        // Menu yang sudah tercatat di detail_pesanan tidak boleh dihapus
        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $ayam = Menu::where('nama', 'Ayam Goreng Lengkuas')->firstOrFail();
        $pesanan = Pesanan::create([
            'user_id' => $budi->id,
            'meja_id' => 1,
            'metode_penerimaan' => 'dine-in',
            'metode_pembayaran' => 'qris',
            'status' => 'selesai',
            'total_harga' => 25000,
        ]);
        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $ayam->id,
            'qty' => 1,
            'subtotal' => 25000,
        ]);

        $this->actingAs($admin)->delete("/admin/menu/{$ayam->id}")
            ->assertRedirect(route('admin.menu.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('menu', ['id' => $ayam->id]);
    }

    public function test_admin_kelola_pesanan_konfirmasi_bayar_dan_cetak_struk(): void
    {
        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();
        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $ayam = Menu::where('nama', 'Ayam Goreng Lengkuas')->firstOrFail();

        $pesanan = Pesanan::create([
            'user_id' => $budi->id,
            'meja_id' => 1,
            'metode_penerimaan' => 'dine-in',
            'metode_pembayaran' => 'qris',
            'status' => 'menunggu_konfirmasi',
            'total_harga' => 25000,
        ]);
        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $ayam->id,
            'qty' => 1,
            'catatan' => 'Sambal pisah',
            'subtotal' => 25000,
        ]);
        $pembayaran = Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'metode' => 'qris',
            'status_pembayaran' => 'menunggu',
            'jumlah' => 25000,
        ]);

        // Konfirmasi pembayaran lunas
        $this->actingAs($admin)->patch("/admin/pesanan/{$pesanan->id}/konfirmasi-bayar")
            ->assertRedirect();
        $this->assertSame('lunas', $pembayaran->fresh()->status_pembayaran);

        // Alur status: dimasak -> siap_diambil -> selesai
        $this->actingAs($admin)->patch("/admin/pesanan/{$pesanan->id}/status", ['status' => 'dimasak'])->assertRedirect();
        $this->assertSame('dimasak', $pesanan->fresh()->status);

        $this->actingAs($admin)->patch("/admin/pesanan/{$pesanan->id}/status", ['status' => 'siap_diambil'])->assertRedirect();
        $this->assertSame('siap_diambil', $pesanan->fresh()->status);

        $this->actingAs($admin)->patch("/admin/pesanan/{$pesanan->id}/status", ['status' => 'selesai'])->assertRedirect();
        $this->assertSame('selesai', $pesanan->fresh()->status);

        // Tolak pesanan dengan alasan_tolak
        $this->actingAs($admin)->patch("/admin/pesanan/{$pesanan->id}/status", [
            'status' => 'ditolak',
            'alasan_tolak' => 'Bahan baku habis',
        ])->assertRedirect();
        $this->assertSame('ditolak', $pesanan->fresh()->status);
        $this->assertSame('Bahan baku habis', $pesanan->fresh()->alasan_tolak);

        // Cetak struk dapur
        $this->actingAs($admin)->get("/admin/pesanan/{$pesanan->id}/struk")
            ->assertStatus(200)
            ->assertSee('window.print()', false)
            ->assertSee('Ayam Goreng Lengkuas')
            ->assertSee('Sambal pisah');
    }

    public function test_admin_crud_meja_otomatis_menghasilkan_qr_code_katalog(): void
    {
        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();

        $this->actingAs($admin)->post('/admin/meja', [
            'nomor_meja' => 'Meja 4 (Lantai Atas)',
        ])->assertRedirect(route('admin.meja.index'));

        $meja = Meja::where('nomor_meja', 'Meja 4 (Lantai Atas)')->firstOrFail();
        $expectedUrl = url('/menu?meja='.$meja->id);
        $this->assertSame($expectedUrl, $meja->qr_code);

        $this->actingAs($admin)->get('/admin/meja')
            ->assertStatus(200)
            ->assertSee('<svg', false)
            ->assertSee($expectedUrl, false);

        $this->actingAs($admin)->put("/admin/meja/{$meja->id}", [
            'nomor_meja' => 'Meja 4 VIP',
        ])->assertRedirect(route('admin.meja.index'));
        $this->assertSame('Meja 4 VIP', $meja->fresh()->nomor_meja);

        $this->actingAs($admin)->delete("/admin/meja/{$meja->id}")
            ->assertRedirect(route('admin.meja.index'));
        $this->assertDatabaseMissing('meja', ['id' => $meja->id]);
    }

    public function test_admin_kelola_pelanggan_dan_export_laporan_pdf_csv(): void
    {
        $admin = User::where('email', 'admindapurbucik@gmail.com')->firstOrFail();
        $budi = User::where('email', 'budisantoso@gmail.com')->firstOrFail();
        $ayam = Menu::where('nama', 'Ayam Goreng Lengkuas')->firstOrFail();

        // Toggle blokir / aktif pelanggan
        $this->actingAs($admin)->patch("/admin/pelanggan/{$budi->id}/toggle-status")->assertRedirect();
        $this->assertSame('diblokir', $budi->fresh()->status_akun);

        $this->actingAs($admin)->patch("/admin/pelanggan/{$budi->id}/toggle-status")->assertRedirect();
        $this->assertSame('aktif', $budi->fresh()->status_akun);

        // Admin tidak boleh memblokir sesama admin
        $this->actingAs($admin)->patch("/admin/pelanggan/{$admin->id}/toggle-status")->assertForbidden();

        // Buat transaksi selesai untuk laporan
        $pesanan = Pesanan::create([
            'user_id' => $budi->id,
            'meja_id' => 1,
            'metode_penerimaan' => 'dine-in',
            'metode_pembayaran' => 'qris',
            'status' => 'selesai',
            'total_harga' => 50000,
        ]);
        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $ayam->id,
            'qty' => 2,
            'subtotal' => 50000,
        ]);
        Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'metode' => 'qris',
            'status_pembayaran' => 'lunas',
            'jumlah' => 50000,
        ]);

        $rangeQuery = 'start_date='.today()->subDays(1)->toDateString().'&end_date='.today()->toDateString();

        $this->actingAs($admin)->get("/admin/laporan?{$rangeQuery}")
            ->assertStatus(200)
            ->assertSee('50.000')
            ->assertSee('Ayam Goreng Lengkuas');

        // Export PDF menggunakan DomPDF
        $pdfRes = $this->actingAs($admin)->get("/admin/laporan/export/pdf?{$rangeQuery}");
        $pdfRes->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $pdfRes->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $pdfRes->getContent());

        // Export CSV (Excel)
        $csvRes = $this->actingAs($admin)->get("/admin/laporan/export/excel?{$rangeQuery}");
        $csvRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $csvRes->headers->get('content-type'));
        $csvBody = $csvRes->streamedContent();
        $this->assertStringContainsString('LAPORAN PENJUALAN DAPUR BUCIK', $csvBody);
        $this->assertStringContainsString('Ayam Goreng Lengkuas', $csvBody);
    }
}
