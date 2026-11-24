<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Mengisi data awal realistis untuk aplikasi Dapur Bucik.
     */
    public function run(): void
    {
        $this->seedUsers();
        $kategoriIds = $this->seedKategori();
        $this->seedMenu($kategoriIds);
        $this->seedMeja();
    }

    /**
     * Membuat akun awal Admin dan Pelanggan.
     */
    private function seedUsers(): void
    {
        $defaultPassword = Hash::make('password');

        $users = [
            [
                'nama' => 'Admin Dapur Bucik',
                'email' => 'admindapurbucik@gmail.com',
                'password' => $defaultPassword,
                'no_hp' => '081234567890',
                'foto_profil' => null,
                'role' => 'admin',
                'status_akun' => 'aktif',
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budisantoso@gmail.com',
                'password' => $defaultPassword,
                'no_hp' => '081298765432',
                'foto_profil' => null,
                'role' => 'pelanggan',
                'status_akun' => 'aktif',
            ],
            [
                'nama' => 'Ismail Pratama',
                'email' => 'ismailpratama@gmail.com',
                'password' => $defaultPassword,
                'no_hp' => '081355556666',
                'foto_profil' => null,
                'role' => 'pelanggan',
                'status_akun' => 'diblokir',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }

    /**
     * Membuat data kategori menu dan mengembalikan peta nama => id.
     *
     * @return array<string, int>
     */
    private function seedKategori(): array
    {
        $namaKategoriList = [
            'Masakan Rumah',
            'Cemilan Tradisional',
            'Minuman Segar',
            'Menu Anak',
        ];

        $kategoriIds = [];

        foreach ($namaKategoriList as $namaKategori) {
            $kategori = Kategori::updateOrCreate(['nama_kategori' => $namaKategori]);
            $kategoriIds[$namaKategori] = $kategori->id;
        }

        return $kategoriIds;
    }

    /**
     * Membuat data menu contoh beserta kategori, harga, promo, dan statusnya.
     *
     * @param  array<string, int>  $kategoriIds
     */
    private function seedMenu(array $kategoriIds): void
    {
        $menus = [
            [
                'kategori_id' => $kategoriIds['Masakan Rumah'],
                'nama' => 'Ayam Goreng Lengkuas',
                'deskripsi' => 'Ayam ungkep bumbu rempah tradisional dengan taburan serundeng lengkuas gurih dan sambal terasi segar.',
                'harga' => 25000.00,
                'harga_promo' => null,
                'foto' => 'images/menu/ayam-goreng-lengkuas.webp',
                'status_ketersediaan' => 'tersedia',
                'rating' => 4.8,
            ],
            [
                'kategori_id' => $kategoriIds['Masakan Rumah'],
                'nama' => 'Sayur Asem',
                'deskripsi' => 'Sayur asem khas rumahan dengan kuah asam segar, jagung manis, labu siam, melinjo, dan kacang panjang.',
                'harga' => 12000.00,
                'harga_promo' => null,
                'foto' => 'images/menu/sayur-asem.webp',
                'status_ketersediaan' => 'habis',
                'rating' => 4.6,
            ],
            [
                'kategori_id' => $kategoriIds['Masakan Rumah'],
                'nama' => 'Nasi Goreng Kampung',
                'deskripsi' => 'Nasi goreng tradisional dengan bumbu ulek bawang merah, terasi pilihan, suwiran ayam, telur ceplok, dan kerupuk.',
                'harga' => 20000.00,
                'harga_promo' => null,
                'foto' => 'images/menu/nasi-goreng-kampung.webp',
                'status_ketersediaan' => 'tersedia',
                'rating' => 4.7,
            ],
            [
                'kategori_id' => $kategoriIds['Masakan Rumah'],
                'nama' => 'Nasi Kuning Bumbu Rempah',
                'deskripsi' => 'Nasi kuning pulen dimasak dengan santan dan kunyit pilihan, disajikan bersama telur balado, orek tempe, dan serundeng.',
                'harga' => 20000.00,
                'harga_promo' => 15000.00,
                'foto' => 'images/menu/nasi-kuning.webp',
                'status_ketersediaan' => 'tersedia',
                'rating' => 4.9,
            ],
            [
                'kategori_id' => $kategoriIds['Cemilan Tradisional'],
                'nama' => 'Pisang Goreng Madu',
                'deskripsi' => 'Pisang kepok matang manis digoreng renyah dengan balutan adonan madu karamel yang harum dan legit.',
                'harga' => 15000.00,
                'harga_promo' => null,
                'foto' => 'images/menu/pisang-goreng-madu.webp',
                'status_ketersediaan' => 'tersedia',
                'rating' => 4.8,
            ],
            [
                'kategori_id' => $kategoriIds['Minuman Segar'],
                'nama' => 'Es Teh Pandan Gula Aren',
                'deskripsi' => 'Seduhan teh melati aromatik dengan daun pandan segar dan sirup gula aren murni.',
                'harga' => 10000.00,
                'harga_promo' => null,
                'foto' => 'images/menu/minuman-segar.webp',
                'status_ketersediaan' => 'tersedia',
                'rating' => 4.7,
            ],
            [
                'kategori_id' => $kategoriIds['Menu Anak'],
                'nama' => 'Paket Bento Ayam Madu Anak',
                'deskripsi' => 'Nasi pulen hangat, potongan ayam goreng madu tanpa tulang yang lembut, dan sayur sop bening.',
                'harga' => 18000.00,
                'harga_promo' => null,
                'foto' => 'images/menu/menu-anak.webp',
                'status_ketersediaan' => 'tersedia',
                'rating' => 4.9,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['nama' => $menu['nama']], $menu);
        }
    }

    /**
     * Membuat daftar meja untuk pemesanan dine-in.
     */
    private function seedMeja(): void
    {
        $mejas = [
            [
                'nomor_meja' => 'Meja 1 (Area Dekat Jendela)',
                'qr_code' => 'DAPURBUCIK-MEJA-01',
            ],
            [
                'nomor_meja' => 'Meja 2 (Area Tengah)',
                'qr_code' => 'DAPURBUCIK-MEJA-02',
            ],
            [
                'nomor_meja' => 'Meja 3 (Teras Luar)',
                'qr_code' => 'DAPURBUCIK-MEJA-03',
            ],
        ];

        foreach ($mejas as $meja) {
            Meja::updateOrCreate(['nomor_meja' => $meja['nomor_meja']], $meja);
        }
    }
}
